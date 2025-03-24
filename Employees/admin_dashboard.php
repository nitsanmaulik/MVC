<?php
session_start();
include '../Config/config.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch Employees & Team Leaders
$users = $conn->query("SELECT id, name, email, role FROM employees");

// Fetch All Tasks
$tasks = $conn->query("
    SELECT t.id, t.title, t.description, e.name AS assigned_to, t.status 
    FROM tasks t 
    JOIN employees e ON t.assigned_to = e.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <h2>Admin Dashboard</h2>
            <div>
                <a href="manage_employees.php" class="btn btn-info">Manage Employees</a>
                <a href="edit_admin_profile.php" class="btn btn-info">Edit Profile</a>
                <button class="btn btn-danger" id="logoutButton">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="container">
            <h3>
            <img src="../<?php echo $_SESSION['admin_photo'] ?>" alt="profile photo" class="rounded-circle" width="100">
                Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
            </h3>
        </div>

        <div class="row mt-4">
            <!-- Assign Task Section -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center">Assign Task</h4>
                    <form action="assign_task_admin.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Task Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Task Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select class="form-control" name="assigned_to" required>
                                <?php while ($user = $users->fetch_assoc()) { ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Assign Task</button>
                    </form>
                </div>
            </div>

            <!-- Add Employee Section -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center">Add New Employee</h4>
                    <form id="registerEmployeeForm" action="register_employee.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                            <small class="text-danger" id="nameError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                            <small class="text-danger" id="emailError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                            <small class="text-danger" id="phoneError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" class="form-control" name="qualification" id="qualification">
                            <small class="text-danger" id="qualificationError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            <small class="text-danger" id="passwordError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role">
                                <option value="employee">Employee</option>
                                <option value="team_leader">Team Leader</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Photo</label>
                            <input type="file" class="form-control" name="photo" id="photo" accept="image/*">
                            <small class="text-danger" id="photoError"></small>
                        </div>

                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('congratulations your Register has done successfull!!');">Register Employee</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ✅ All Assigned Tasks Section -->
        <h4 class="mt-4 text-center">All Assigned Tasks</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Update Progress</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $tasks->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['title']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td><?php echo htmlspecialchars($task['assigned_to']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($task['status'] == 'completed') ? 'success' : (($task['status'] == 'in_progress') ? 'warning' : 'secondary'); ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </span>
                            </td>
                            <td>
                                
                                <form action="update_task_progress_admin.php" method="POST">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <select name="status" class="form-select">
                                        <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <button type="submit" class="btn btn-success btn-sm mt-2">Update</button>
                                </form>
                            </td>
                            <td class="task-buttons">
                                <!-- Update Task -->
                                <form action="update_task_admin.php" method="POST">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Edit Task</button>
                                </form>
                                <a href="admin_delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../Assets/JS/register_validation.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const logoutButton = document.getElementById("logoutButton");

        if (logoutButton) {
            logoutButton.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent immediate logout
                let confirmLogout = confirm("Are you sure you want to logout?");
                if (confirmLogout) {
                    window.location.href = "admin_logout.php"; // Redirect to logout page
                }
            });
        }
    });
</script>
</body>
</html>
