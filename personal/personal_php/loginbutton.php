<?php
session_start();
require_once "db.php";   // gives you $mysqli connection

// This code runs ONLY when the login button is pressed
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $identifier = trim($_POST['identifier']);  // username or email
    $password   = $_POST['password'];

    // If fields empty
    if ($identifier === "" || $password === "") {
        echo "Please fill in all fields.";
        exit;
    }

    // Search for user by username or email
    $stmt = $mysqli->prepare("SELECT id, username, password_hash 
                              FROM users 
                              WHERE username = ? OR email = ? 
                              LIMIT 1");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $stmt->store_result();

    // If user found
    if ($stmt->num_rows === 1) {

        $stmt->bind_result($id, $username, $hash);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hash)) {

            // Login success — set session
            session_regenerate_id(true);
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;

            // Redirect to protected page
            header("Location: protected.php");
            exit;

        } else {
            echo "Incorrect password.";
        }

    } else {
        echo "User not found.";
    }

    $stmt->close();
}
?>
