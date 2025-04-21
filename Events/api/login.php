<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the session
session_start();

// Include database configuration
require_once '../config/database.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if username is empty
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter username.";
        } else {
            $username = trim($_POST["username"]);
        }

        // Check if password is empty
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate credentials
        if (empty($username_err) && empty($password_err)) {
            $sql = "SELECT id, username, password FROM users WHERE username = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        if (mysqli_stmt_fetch($stmt)) {
                            if (password_verify($password, $hashed_password)) {
                                // Password is correct, start a new session
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                
                                // Return success response
                                echo json_encode([
                                    "status" => "success",
                                    "message" => "Login successful!",
                                    "redirect" => "project.html"
                                ]);
                                exit;
                            } else {
                                echo json_encode([
                                    "status" => "error",
                                    "message" => "Invalid username or password."
                                ]);
                                exit;
                            }
                        }
                    } else {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Invalid username or password."
                        ]);
                        exit;
                    }
                } else {
                    throw new Exception("Database query failed: " . mysqli_error($conn));
                }
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Prepare statement failed: " . mysqli_error($conn));
            }
        } else {
            echo json_encode([
                "status" => "error",
                "errors" => [
                    "username" => $username_err,
                    "password" => $password_err
                ]
            ]);
            exit;
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode([
            "status" => "error",
            "message" => "An error occurred. Please try again later.",
            "debug" => $e->getMessage()
        ]);
        exit;
    }
    mysqli_close($conn);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit;
}
?> 