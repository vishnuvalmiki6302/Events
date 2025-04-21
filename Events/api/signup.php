<?php
header('Content-Type: application/json');

// Include database configuration
require_once '../config/database.php';

// Initialize variables
$username = $email = $password = $full_name = $phone = "";
$username_err = $email_err = $password_err = $full_name_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format";
        }
    }

    // Validate full name
    if (empty(trim($_POST["full_name"]))) {
        $full_name_err = "Please enter your full name.";
    } else {
        $full_name = trim($_POST["full_name"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Get phone number (optional)
    $phone = trim($_POST["phone"]);

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($email_err) && empty($full_name_err)) {
        $sql = "INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_email, $param_password, $param_full_name, $param_phone);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_full_name = $full_name;
            $param_phone = $phone;

            if (mysqli_stmt_execute($stmt)) {
                // Registration successful
                echo json_encode(["status" => "success", "message" => "Registration successful!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again later."]);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        // Return validation errors
        echo json_encode([
            "status" => "error",
            "errors" => [
                "username" => $username_err,
                "email" => $email_err,
                "password" => $password_err,
                "full_name" => $full_name_err
            ]
        ]);
    }
    mysqli_close($conn);
}
?> 