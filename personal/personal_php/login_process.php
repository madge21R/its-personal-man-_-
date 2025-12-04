<?php
session_start();
require_once "db.php";   // connects to users_db

if (isset($_POST['login_btn'])) {

    $identifier = trim($_POST['identifier']);
    $password   = $_POST['password'];

    // 1. Validate fields
    if ($identifier === "" || $password === "") {
        echo "<script>alert('Please fill in all fields'); window.location.href='login.php';</script>";
        exit;
    }

    // 2. Look for user by username or email
    $stmt = $mysqli->prepare("SELECT id, username, password_hash 
                              FROM users 
                              WHERE username = ? OR email = ? 
                              LIMIT 1");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $stmt->store_result();

    // 3. If found, check password
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {

            // 4. Login successful
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Redirect to dashboard
            header("Location: index.html");
            exit;

        } else {
            echo "<script>alert('Incorrect password'); window.location.href='login.php';</script>";
        }

    } else {
        echo "<script>alert('User not found'); window.location.href='login.php';</script>";
    }

    $stmt->close();
}
?>
