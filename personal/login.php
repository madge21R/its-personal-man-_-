<?php
// Database connection
$servername = "localhost";
$username = "root";   // default in phpMyAdmin
$password = "";       // your MySQL root password
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$user = $_POST['username'];
$email = $_POST['email'];
$pass = $_POST['password'];

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Verify password
    if (password_verify($pass, $hashed_password)) {
        echo "✅ Login successful! Welcome, " . htmlspecialchars($user);
        // You could redirect: header("Location: dashboard.php");
    } else {
        echo "❌ Invalid password.";
    }
} else {
    echo "❌ No user found.";
}

$stmt->close();
$conn->close();
?>