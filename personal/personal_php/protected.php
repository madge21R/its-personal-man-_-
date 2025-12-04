<?php
// protected.php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Protected Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Welcome, <?php echo $username; ?>!</h1>
  <p>This page is protected and only visible to logged-in users.</p>
  <p><a href="logout.php">Log out</a></p>
</body>
</html>
