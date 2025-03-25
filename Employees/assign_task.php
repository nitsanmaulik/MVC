
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
