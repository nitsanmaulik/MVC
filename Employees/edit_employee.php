<?php
session_start();
include '../Config/config.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get Employee ID
if (!isset($_GET['id'])) {
    die("Error: Employee ID missing.");
}

$employee_id = $_GET['id'];

// Fetch employee details
$stmt = $conn->prepare("SELECT name, email, phone, qualification, role FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    die("Error: Employee not found.");
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $qualification = trim($_POST["qualification"]);
    $role = $_POST["role"];

    $stmt = $conn->prepare("UPDATE employees SET name=?, email=?, phone=?, qualification=?, role=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $qualification, $role, $employee_id);

    if ($stmt->execute()) {
        header("Location: manage_employees.php?success=Employee Updated");
        exit();
    } else {
        echo "Error updating employee: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/CSS/dashboard.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Employee</h2>
        <form action="edit_employee.php?id=<?php echo $employee_id; ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Qualification</label>
                <input type="text" class="form-control" name="qualification" value="<?php echo htmlspecialchars($employee['qualification']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select class="form-control" name="role">
                    <option value="employee" <?php echo ($employee['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                    <option value="team_leader" <?php echo ($employee['role'] == 'team_leader') ? 'selected' : ''; ?>>Team Leader</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Employee</button>
            <a href="manage_employees.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>
