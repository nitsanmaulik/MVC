<?php
session_start();
include '../Config/config.php';

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

if (!isset($_GET['id'])) {
    die("Error: Employee ID is missing.");
}

$employee_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->close();

header("Location: manage_employees.php?success=Employee Deleted");
exit();
?>
