<?php
session_start();
include '../Config/config.php';

// Debugging: Check if session variables are set properly
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access - Session not set properly.");
}

// Debugging: Print role value to check if it's set correctly
$role = $_SESSION['role']; 
error_log("User Role: " . $role); // Logs role in the error log

$user_id = $_SESSION['user_id'];

// Ensure Task ID is provided
if (!isset($_GET['id']) && !isset($_POST['task_id'])) {
    die("Error: Task ID is missing.");
}

$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : $_GET['id'];

// Fetch task details
$stmt = $conn->prepare("SELECT title, description, assigned_to FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
$stmt->close();

if (!$task) {
    die("Error: Task not found.");
}

// Fetch employees for assignment
$employees = $conn->query("SELECT id, name FROM employees WHERE role = 'employee'");

// Handle Task Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'], $_POST['assigned_to'])) {
    $new_title = trim($_POST['title']);
    $new_description = trim($_POST['description']);
    $new_assigned_to = $_POST['assigned_to'];

    $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, assigned_to = ? WHERE id = ?");
    $stmt->bind_param("ssii", $new_title, $new_description, $new_assigned_to, $task_id);

    if ($stmt->execute()) {
        // ✅ Corrected Role-Based Redirection
        if ($role === 'admin') {
            header("Location: ../Employees/admin_dashboard.php?success=Task Updated");
        } elseif ($role === 'team_leader') {
            header("Location: ../Employees/team_leader_dashboard.php?success=Task Updated");
        } else {
            die("Error: Unknown role '$role'. Check database values.");
        }
        exit();
    } else {
        echo "Error updating task: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Task</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Task</h2>
        <form action="update_task.php" method="POST">
            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id); ?>">

            <div class="mb-3">
                <label class="form-label">Task Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($task['title'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Task Description</label>
                <textarea class="form-control" name="description" required><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Assign To</label>
                <select name="assigned_to" class="form-control">
                    <?php while ($employee = $employees->fetch_assoc()) { ?>
                        <option value="<?php echo $employee['id']; ?>" <?php echo ($employee['id'] == ($task['assigned_to'] ?? '')) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($employee['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Task</button>

            <!-- ✅ Fixed Cancel Button Redirect -->
            <a href="<?php echo ($role === 'admin') ? '../Employees/admin_dashboard.php' : '../Employees/team_leader_dashboard.php'; ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>
