<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer autoloader via Composer
include 'db.php'; // Your DB connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // Check if email exists first
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists, now update token and expiry
        $stmt->close();
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        if ($stmt->execute()) {
            // Send email with PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@gmail.com';        // ✅ your Gmail
                $mail->Password = 'your_app_password';           // ✅ Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Debug output (optional for testing)
                $mail->SMTPDebug = 2; // Remove or set to 0 after testing
                $mail->Debugoutput = 'html';

                // Recipients
                $mail->setFrom('your_email@gmail.com', 'Leave System');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Link';
                $resetLink = "http://localhost/reset_password.php?token=" . urlencode($token);
                $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password. This link will expire in 1 hour.";

                $mail->send();
                $message = "✅ If this email is registered, a reset link has been sent.";
            } catch (Exception $e) {
                $message = "❌ Email could not be sent. Error: " . $mail->ErrorInfo;
            }
        } else {
            $message = "❌ Failed to update token in database.";
        }
    } else {
        // Do not reveal that email is invalid for security
        $message = "✅ If this email is registered, a reset link has been sent.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        input[type=email], input[type=submit] {
            padding: 10px; margin-top: 10px; width: 250px;
        }
        p { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Reset Password</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your email" required><br><br>
        <input type="submit" value="Send Reset Link">
    </form>
</body>
</html>
