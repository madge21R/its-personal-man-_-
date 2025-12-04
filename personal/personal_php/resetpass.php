<?php
require "db.php";

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

$stmt = $mysqli->prepare("SELECT email, expires FROM password_resets WHERE token = ? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    die("Invalid or expired token.");
}

$stmt->bind_result($email, $expires);
$stmt->fetch();

if ($expires < time()) {
    die("Reset link expired.");
}
?>

