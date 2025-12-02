<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Hash the password
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $hashed_pass);

    if ($stmt->execute()) {
        echo "✅ Registration successful! <a href='login.html'>Login now</a>";
    } else {
        echo "❌ Error: Username or email already exists.";
    }

    $stmt->close();
}

$conn->close();
?>
