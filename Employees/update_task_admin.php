<?php
session_start();
include '../Config/config.php';

// Check if the user is an Admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     die("Unauthorized access.");
// }

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

$task_id = $_GET['id'] ?? $_POST['task_id'] ?? null;

if (!$task_id) {
    die("Error: Task ID is missing.");
}

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

// Fetch employees and team leaders for assignment
$employees = $conn->query("SELECT id, name FROM employees");

// Handle Task Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'], $_POST['assigned_to'])) {
    $new_title = trim($_POST['title']);
    $new_description = trim($_POST['description']);
    $new_assigned_to = $_POST['assigned_to'];

    $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, assigned_to = ? WHERE id = ?");
    $stmt->bind_param("ssii", $new_title, $new_description, $new_assigned_to, $task_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?success=Task Updated");
        exit();
    } else {
        echo "Error updating task: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Task (Admin)</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Task (Admin)</h2>
        <form action="update_task_admin.php" method="POST">
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
            <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>
