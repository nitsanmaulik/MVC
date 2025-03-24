<?php
session_start();
include '../Config/config.php';

// Check if the user is a Team Leader
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader') {
    header('Location: ../login.php');
    exit();
}

$teamLeaderId = $_SESSION['user_id'];
$teamLeaderName = $_SESSION['name'];

// Fetch all employees (excluding team leaders)
$employees = $conn->query("SELECT id, name, email FROM employees WHERE role = 'employee'");

// Fetch only tasks assigned to Employees by this Team Leader
$tasks = $conn->query("
    SELECT t.id, t.title, t.description, e.name AS employee_name, t.status 
    FROM tasks t 
    JOIN employees e ON t.assigned_to = e.id 
    WHERE t.assigned_by = $teamLeaderId AND e.role = 'employee'
");

// Fetch tasks assigned TO this Team Leader (for "My Tasks" modal)
$myTasks = $conn->query("
    SELECT id, title, description, status FROM tasks 
    WHERE assigned_to = $teamLeaderId
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Leader Dashboard</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <h2>Team Leader Dashboard</h2>
            <div>
            
                <a href="edit_profile.php" class="btn btn-warning">Edit Profile</a>
                
                
                <!-- My Tasks Button -->
                <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#myTasksModal">
                    My Tasks
                </button>
                <!-- Logout Button -->
                <button class="btn btn-danger" id="logoutButton">Logout</button>

        </div>
    </nav>

    <div class="container mt-4">
        <div class="container">
            <h2><img src="../<?php echo $_SESSION['photo'] ?>" alt="profile photo" class="rounded-circle" width="100">
            Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (Team Leader)</h2>
        </div>
        <!-- Assign Task Form -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center">Assign Task</h4>
                    <form action="assign_task.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Task Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-control" id="assigned_to" name="assigned_to" required>
                                <?php while ($employee = $employees->fetch_assoc()) { ?>
                                    <option value="<?php echo $employee['id']; ?>">
                                        <?php echo htmlspecialchars($employee['name']); ?> (<?php echo htmlspecialchars($employee['email']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Assign Task</button>
                    </form>
                </div>
            </div>

            <!-- Employee Tasks Table -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center">Assigned Employee Tasks</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($task = $tasks->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                                        <td><?php echo htmlspecialchars($task['employee_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($task['status'] == 'completed') ? 'success' : (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); ?>">
                                                <?php echo htmlspecialchars($task['status']); ?>
                                            </span>
                                        </td>
                                        <td class="task-buttons">
                                            <!-- Edit Task Button -->
                                            <form action="update_task.php" method="POST" class="d-inline">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-warning btn-sm">Edit Task</button>
                                            </form>

                                            <!-- Delete Task Button -->
                                            <form action="delete_task.php" method="POST" class="d-inline">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?');">Delete</button>
                                            </form>
                                        </td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Tasks Modal -->
    <div class="modal fade" id="myTasksModal" tabindex="-1" aria-labelledby="myTasksLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myTasksLabel">My Assigned Tasks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                            <?php while ($task = $myTasks->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($task['status'] == 'completed') ? 'success' : (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); ?>">
                                            <?php echo htmlspecialchars($task['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                    <!-- Update Progress Form (Does Not Redirect to Update Task Page) -->
                                    <form action="update_task_progress_team_leader.php" method="POST" class="d-inline">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <select name="status" class="form-select d-inline w-auto">
                                            <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm">Update Progress</button>
                                    </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const logoutButton = document.getElementById("logoutButton");

        if (logoutButton) {
            logoutButton.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent immediate logout
                let confirmLogout = confirm("Are you sure you want to logout?");
                if (confirmLogout) {
                    window.location.href = "logout.php"; // Redirect to logout page
                }
            });
        }
    });
</script>

</body>
</html>
