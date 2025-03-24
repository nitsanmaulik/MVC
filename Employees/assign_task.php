<!-- <?php
session_start();
include '../Config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ensure Task ID is provided
// if (!isset($_POST['task_id'])) {
//     die("Error: Task ID is missing.");
// }

$task_id = $_POST['task_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
    $new_status = $_POST['status'];

    // Only update status, do not change other task details
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
?> -->


<?php
session_start();
include '../Config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $assigned_to = intval($_POST['assigned_to']);
    $assigned_by = $_SESSION['user_id']; // Get logged-in user ID

    // Validate inputs
    if (empty($title) || empty($description) || empty($assigned_to)) {
        die("Error: All fields are required.");
    }

    // Insert task into database
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, assigned_by, assigned_to, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssii", $title, $description, $assigned_by, $assigned_to);

    if ($stmt->execute()) {
        header("Location: team_leader_dashboard.php?success=Task Assigned");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

