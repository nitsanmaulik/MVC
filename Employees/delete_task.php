<?php
session_start();
include '../Config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access.");
}

// Ensure Task ID is provided
if (!isset($_POST['task_id'])) {
    die("Error: Task ID is missing.");
}

$task_id = $_POST['task_id'];

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);

if ($stmt->execute()) {
    // Redirect to the correct dashboard based on role
    $redirect_page = ($_SESSION['role'] === 'admin') ? "admin_dashboard.php" : "team_leader_dashboard.php";
    header("Location: $redirect_page?success=Task Deleted");
    exit();
} else {
    echo "Error deleting task: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
