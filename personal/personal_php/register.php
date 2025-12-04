<?php
// register.php
session_start();
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim and fetch
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2= $_POST['password2'] ?? '';

    // Basic validation
    if ($username === '') $errors[] = 'Username required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $password2) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check if username or email exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username or email already taken.';
            $stmt->close();
        } else {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $insert->bind_param('sss', $username, $email, $hash);
            if ($insert->execute()) {
                $insert->close();
                // Optionally log user in immediately
                $_SESSION['user_id'] = $mysqli->insert_id;
                $_SESSION['username'] = $username;
                header('Location: protected.php');
                exit;
            } else {
                $errors[] = 'Database error during registration.';
                error_log("Register error: " . $mysqli->error);
            }
        }
    }
}
?>
