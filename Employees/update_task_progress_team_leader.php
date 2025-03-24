<?php
session_start();
include '../Config/config.php';

// Ensure only team leaders can update progress
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
        die("Error: Missing task details.");
    }

    $task_id = intval($_POST['task_id']);
    $new_status = $_POST['status'];

    // Validate status
    $valid_statuses = ['pending', 'in_progress', 'completed'];
    if (!in_array($new_status, $valid_statuses)) {
        die("Error: Invalid status.");
    }

    // Update progress in database
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $task_id);

    if ($stmt->execute()) {
        header("Location: team_leader_dashboard.php?success=Task Progress Updated");
        exit();
    } else {
        echo "Error updating task progress: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
