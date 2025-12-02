<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if (password_verify($pass, $hashed_password)) {
        // Store session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $user;

        header("Location: dashboard.php");
        exit();
    } else {
        echo "❌ Invalid password.";
    }

} else {
    echo "❌ No user found.";
}

$stmt->close();
$conn->close();
?>
