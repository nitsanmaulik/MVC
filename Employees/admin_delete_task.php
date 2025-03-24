<?php

session_start();
include '../Config/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'team_leader'){
    header('location: admin_login.php');
}

if(isset($_GET['id'])){
    $task_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param('i', $task_id);

    if($stmt->execute()){
        header('location: ../Employees/admin_dashboard.php?success = task deleted');
        exit();
    } else {
        die($stmt->error);
    }
}
?>
