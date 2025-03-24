<?php
session_start();
include '../Config/config.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$stmt = $conn->prepare("SELECT name, email, photo FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    die("Error: Admin not found.");
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = !empty($_POST["password"]) ? password_hash($_POST["password"], PASSWORD_DEFAULT) : null;

    // Handle Image Upload
    $photo_path = $admin['photo']; // Keep old photo if no new upload

    if (!empty($_FILES["photo"]["name"])) {
        $upload_dir = "../Assets/Images/";
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

        // Move the file to the correct directory
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file; // Store correct file path in DB
        } else {
            die("Error uploading file.");
        }
    }

    // Update admin details
    if ($password) {
        $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, password=?, photo=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $password, $photo_path, $admin_id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, photo=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $photo_path, $admin_id);
    }

    if ($stmt->execute()) {
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_photo'] = $photo_path;
        header("Location: admin_dashboard.php?success=Profile Updated");
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Admin Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profile</h2>
        <form action="edit_admin_profile.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (Leave blank to keep current password)</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div class="mb-3">
                <label class="form-label">Upload New Photo</label>
                <input type="file" class="form-control" name="photo" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('your profile edited successfully!!');">Update Profile</button>
            <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>
