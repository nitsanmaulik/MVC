<?php
session_start();
include '../Config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access.");
}

$assigned_by = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $assigned_to = intval($_POST['assigned_to']);

    // Validate inputs
    if (empty($title) || empty($description) || empty($assigned_to)) {
        die("All fields are required.");
    }

    // Assign task based on user role
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, assigned_by, assigned_to) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $title, $description, $assigned_by, $assigned_to);

    if ($stmt->execute()) {
        header("Location: " . ($role === 'admin' ? "team_leader_dashboard.php" : "admin_dashboard.php") . "?success=Task Assigned");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
