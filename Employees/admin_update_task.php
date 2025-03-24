<?php
    session_start();
    include '../Config/config.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader') {
        header('location: login.php');
        exit();
    }

    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        $query = $conn->prepare('SELECT * FROM tasks WHERE id = ?');
        $query->bind_param('i',$task_id);
        $query->execute();
        $task = $query->get_result()->fetch_assoc();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $task_id = $_POST['task_id'];

        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ?, WHERE id = ?");
        $stmt->bind_param('sssi', $title, $description, $status, $task_id);

        if($stmt->execute()){
            header("Location: ../Employees/team_leader_dashboard.php?success=Task Updated");
        } else {
            echo "Error: " . $stmt->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Task</title>
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Update Task</h2>
        <form action="update_task.php" method="POST">
            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Task Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo $task['title']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Task Description</label>
                <textarea class="form-control" name="description" required><?php echo $task['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-control" name="status">
                    <option value="pending" <?php if ($task['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="in_progress" <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                    <option value="completed" <?php if ($task['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update Task</button>
            <a href="../Employees/admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>