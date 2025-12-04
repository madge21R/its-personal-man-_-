<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Email exists — create reset token
        $token = bin2hex(random_bytes(32));
        $expires = date("U") + 1800; // 30 minutes

        // Insert token into DB
        $stmt->close();
        $stmt = $mysqli->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE token=?, expires=?");
        $stmt->bind_param("ssiss", $email, $token, $expires, $token, $expires);
        $stmt->execute();

        // Reset link
        $resetLink = "http://yourwebsite.com/reset_password.php?token=" . $token;

        // SEND EMAIL (simple mail function)
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password:\n$resetLink";
        $headers = "From: no-reply@yourwebsite.com";

        mail($email, $subject, $message, $headers);

        echo "A password reset link has been sent to your email.";
        exit;
    } else {
        echo "No account found with that email.";
    }
}
?>

