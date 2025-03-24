<?php
session_start();
include '../Config/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] === 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, phone, qualification, photo FROM employees WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $qualification = trim($_POST['qualification']);
    $photo = $user['photo']; // Keep existing photo by default
    $new_password = trim($_POST['password']);

    // Handle File Upload
    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = "../Assets/Images/";
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $photo = $photo_path;
        } else {
            echo "Error uploading file.";
        }
    }

    // Update Query (With Password Update if Provided)
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE employees SET name = ?, email = ?, phone = ?, qualification = ?, photo = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $qualification, $photo, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE employees SET name = ?, email = ?, phone = ?, qualification = ?, photo = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $qualification, $photo, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $photo;
        header("Location: " . ($role === 'team_leader' ? "team_leader_dashboard.php" : "employee_dashboard.php"));
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Assets/CSS/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profile</h2>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                <small class="text-danger" id="nameError"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                <small class="text-danger" id="emailError"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                <small class="text-danger" id="phoneError"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Qualification</label>
                <input type="text" class="form-control" name="qualification" value="<?php echo htmlspecialchars($user['qualification']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Photo</label>
                <input type="file" class="form-control" name="photo">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($user['photo']); ?>" class="mt-2 rounded-circle" width="100">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (Optional)</label>
                <input type="password" class="form-control" name="password" placeholder="Enter new password">
            </div>
            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('your profile updated successfully!!');">Update Profile</button>
            <a href="<?php echo ($role === 'team_leader') ? 'team_leader_dashboard.php' : 'employee_dashboard.php'; ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
    <script src="../Assets/JS/validation.js"></script>
</body>
</html>
