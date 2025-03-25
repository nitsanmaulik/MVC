
<?php
session_start();
include '../Config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust path if necessary


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
        $upload_dir = "../Assets/Images/";
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $upload_dir . $photo_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $file_size = $_FILES["photo"]["size"];

        // Validate file type
        $allowed_types = ["jpg", "jpeg", "png"];
        if (!in_array($file_type, $allowed_types)) {
            die("Error: Only JPG, JPEG, and PNG files are allowed.");
        }

        // Validate file size (max 2MB)
        if ($file_size > 2000000) {
            die("Error: File size must be 2MB or less.");
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            die("Error uploading file.");
        }
    }

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO employees (name, email, password, phone, qualification, role, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $password, $phone, $qualification, $role, $photo_path);

    if ($stmt->execute()) {
        sendRegistrationEmail($email, $name, $_POST["password"]); // Send email notification
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// **Function to Send Email**
function sendRegistrationEmail($email, $name, $plainPassword) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change this if using another provider
        $mail->SMTPAuth = true;
        $mail->Username = 'maulikkikani.nitsan@gmail.com'; // Your email
        $mail->Password = 'megi ytyu egfo ntpy'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //$mail->SMTPSecure = 'tls';
        $mail->SMTPDebug = 2; // Debug Level: 2 (show full SMTP logs)
        $mail->Debugoutput = 'html'; // Show debug output in HTML
        $mail->Port = 587;
        // Enable Debugging
        $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

        // Email Settings
        $mail->setFrom('maulikkikani.nitsan@gmail.com', 'NITSAN');
        $mail->addAddress($email,$name );
        $mail->Subject = 'Welcome to Our Company!';
        $mail->isHTML(true);
        $mail->Body = "
            <h3>Dear $name,</h3>
            <p>Congratulations! Your account has been created.</p>
            <p><b>Email:</b> $email</p>
            <p><b>Password:</b> $plainPassword</p>
            <p>You can now log in to your account.</p>
            <br>
            <p>Best Regards,</p>
            <p>NITSAN</p>
        ";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>

