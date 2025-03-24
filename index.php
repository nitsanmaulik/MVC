<?php
session_start();
include 'Config/config.php';

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
        $upload_dir = __DIR__ . "/Assets/Images/"; // Absolute path to the folder
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $upload_dir . $photo_name;
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

        // Ensure directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move file to directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = "Assets/Images/" . $photo_name; // Store relative path
        } else {
            die("Error uploading file.");
        }
    }

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO employees (name, email, password, phone, qualification, role, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $password, $phone, $qualification, $role, $photo_path);

    if ($stmt->execute()) {
        header('Location: login.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Assets/CSS/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Employee Registration</h2>
        <form id="registrationForm" action="index.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name"  id="name">
                <small class="text-danger" id="nameError"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email">
                <small class="text-danger" id="emailError"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password">
                <small class="text-danger" id="passwordError"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone">
                <small class="text-danger" id="phoneError"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Qualification</label>
                <input type="text" class="form-control" name="qualification" id="qualification">
                <small class="text-danger" id="qualificationError"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select class="form-control" name="role">
                    <option value="employee">Employee</option>
                    <option value="team_leader">Team Leader</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Photo</label>
                <input type="file" class="form-control" name="photo" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('congratulations your Register has done successfull!!');">Register</button>
        </form>
    </div>
    <script src="../Assets/JS/validation.js"></script>
</body>
</html>
