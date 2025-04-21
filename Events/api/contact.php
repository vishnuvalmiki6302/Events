<?php
header('Content-Type: application/json'); // Ensure proper JSON response

require_once '../config/database.php';

$name = $email = $subject = $message = "";
$name_err = $email_err = $subject_err = $message_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format";
        }
    }

    // Validate subject
    if (empty(trim($_POST["subject"]))) {
        $subject_err = "Please enter a subject.";
    } else {
        $subject = trim($_POST["subject"]);
    }

    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = trim($_POST["message"]);
    }

    // If no errors, insert into DB
    if (empty($name_err) && empty($email_err) && empty($subject_err) && empty($message_err)) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(["status" => "success", "message" => "Data received successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again later."]);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "errors" => [
                "name" => $name_err,
                "email" => $email_err,
                "subject" => $subject_err,
                "message" => $message_err
            ]
        ]);
    }
    mysqli_close($conn);
}
?>
