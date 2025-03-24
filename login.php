<?php
session_start();
include 'Config/config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, password, role, photo FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $role, $photo);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $name;
            $_SESSION['photo'] = $photo;

            if ($role == 'employee') {
                header("Location: ../Employees/employee_dashboard.php");
            } elseif ($role == 'team_leader') {
                header("Location: ../Employees/team_leader_dashboard.php");
            }
            exit();
        } else {
            echo "<script>confirm('Invalid email or password.');</script>";
        }
    } else {
        echo "<script>confirm('Invalid email or password.');</script>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../Assets/CSS/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login</h2>
        <form id="loginForm" action="login.php" method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                <small class="text-danger" id="emailError"></small>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                <small class="text-danger" id="passwordError"></small>
            </div>
            <button type="submit" class="btn btn-custom w-100">Login</button>
        </form>
    </div>
    <script src="../Assets/JS/admin_login_validation.js"></script>

</body>
</html>