<?php
session_start();
include '../Config/config.php';

// Check if user is logged in

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php"); // Redirect to correct login page
    exit();
}



// Ensure Task ID and Status are provided
if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
    die("Error: Task ID or Status is missing.");
}

$task_id = $_POST['task_id'];
$new_status = $_POST['status'];

// Update only the task status
$stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $task_id);

if ($stmt->execute()) {
    header("Location: ../Employees/employee_dashboard.php?success=Task Progress Updated");
    exit();
} else {
    echo "Error updating task progress: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
