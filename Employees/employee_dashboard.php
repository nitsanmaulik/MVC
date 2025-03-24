<?php
session_start();
include '../Config/config.php';

// Ensure user is logged in and is an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Store session user_id

// Fetch employee details from the database
$stmt = $conn->prepare("SELECT name, photo FROM employees WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Error: User not found.");
}

// Assign values
$name = $user['name'];
$photo = $user['photo'] ? $user['photo'] : "../Assets/Images/default.png";

// Fetch tasks assigned to the logged-in employee
$stmt = $conn->prepare("SELECT id, title, description, status FROM tasks WHERE assigned_to = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ">
        <div class="container">
            <h2>Employee Dashboard</h2>
            <div>
            <a href="edit_profile.php" class="btn btn-warning">Edit Profile</a>
                <button id="logoutButton" class="btn btn-danger ms-3">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2><img src="<?php echo htmlspecialchars($photo); ?>" alt="Profile Picture" class="rounded-circle" width="50">
            Welcome, <?php echo htmlspecialchars($name); ?></h2>

        <!-- Task List -->
        <h4 class="mt-4 text-center">Your Assigned Tasks</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Update Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $tasks->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['title']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($task['status'] == 'completed') ? 'success' : (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form action="update_task_progress.php" method="POST">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <select name="status" class="form-control">
                                        <option value="pending" <?php if ($task['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="in_progress" <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                                        <option value="completed" <?php if ($task['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("logoutButton").addEventListener("click", function(event) {
            event.preventDefault();
            let confirmLogout = confirm("Are you sure you want to logout?");
            if (confirmLogout) {
                window.location.href = "logout.php";
            }
        });
    </script>
</body>
</html>
