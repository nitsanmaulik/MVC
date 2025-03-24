<?php
class Validator {
    public static function validateUsername($username) {
        if (empty($username)) {
            return "Username is required.";
        } elseif (!preg_match("/^[a-zA-Z0-9_]{3,15}$/", $username)) {
            return "Username must be 3-15 characters long and contain only letters, numbers, and underscores.";
        }
        return "";
    }

    public static function validateEmail($email) {
        if (empty($email)) {
            return "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }
        return "";
    }

    public static function validatePassword($password) {
        if (empty($password)) {
            return "Password is required.";
        } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
            return "Password must be at least 8 characters long, include a number and an uppercase letter.";
        }
        return "";
    }

    public static function validateBirthdate($birthdate) {
        if (empty($birthdate)) {
            return "Birthdate is required.";
        }
        $dob = strtotime($birthdate);
        $minAge = strtotime("-18 years");
        if ($dob > $minAge) {
            return "You must be at least 18 years old.";
        }
        return "";
    }

    public static function validateGender($gender) {
        return empty($gender) ? "Please select your gender." : "";
    }

    public static function validateEducation($education) {
        return empty($education) ? "Please select at least one education option." : "";
    }
}


require_once "Config/config.php";

class Database {
    public static function registerUser($username, $email, $password, $gender, $education, $birthdate) {
        global $conn;
    
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $conn->prepare("INSERT INTO Employees (Name, Email, Password, Gender, Education, Birthdate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $username, $email, $hashedPassword, $gender, $education, $birthdate);
    
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Database Insert Error: " . $stmt->error);
            return false;
        }
    }
    
    
}


?>
