<?php
session_start();
include '../Config/config.php';

// Ensure Admin is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Validate Task ID and Status
if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
    die("Error: Missing task details.");
}

$task_id = $_POST['task_id'];
$status = $_POST['status'];

// Update Task Progress
$stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $task_id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?success=Task Updated");
    exit();
} else {
    echo "Error updating task: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
