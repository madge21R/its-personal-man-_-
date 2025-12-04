<?php
// login.php
session_start();
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // username or email
    $password   = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $errors[] = 'Please provide username/email and password.';
    } else {
        // fetch user by username OR email
        $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param('ss', $identifier, $identifier);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $username, $password_hash);
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                // success
                session_regenerate_id(true);
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header('Location: protected.php');
                exit;
            } else {
                $errors[] = 'Invalid credentials.';
            }
        } else {
            $errors[] = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Login</h1>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="login.php" autocomplete="off">
    <label>Username or Email
      <input type="text" name="identifier" required value="<?php echo htmlspecialchars($identifier ?? ''); ?>">
    </label><br>

    <label>Password
      <input type="password" name="password" required>
    </label><br>

    <button type="submit">Log In</button>
  </form>

  <p>Need an account? <a href="register.php">Register</a></p>

    <p><a href="forgot_password.php">Forgot Password?</a></p>

</body>

</html>
   