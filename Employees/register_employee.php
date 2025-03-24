<?php
session_start();
include '../Config/config.php';

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $phone = trim($_POST["phone"]);
    $qualification = trim($_POST["qualification"]);
    $role = $_POST["role"];

    // Handle Image Upload
    $photo_path = null;

    if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "../Assets/Images/";
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $file_size = $_FILES["photo"]["size"];

        // Validate file type (only JPG, PNG, JPEG allowed)
        $allowed_types = ["jpg", "jpeg", "png"];
        if (!in_array($file_type, $allowed_types)) {
            die("Error: Only JPG, JPEG, and PNG files are allowed.");
        }

        // Validate file size (max 2MB)
        if ($file_size > 2000000) {
            die("Error: File size must be 2MB or less.");
        }

        // Move the file to the target directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file; // Set correct file path
        } else {
            die("Error uploading file.");
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO employees (name, email, password, phone, qualification, role, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $password, $phone, $qualification, $role, $photo_path);

    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
