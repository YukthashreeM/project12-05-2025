<?php
session_start();
include('db.php'); // your PDO connection

$message = "";

// Default dashboard
$dashboardLink = 'dashboard.php';
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'Admin':
            $dashboardLink = 'admin.php';
            break;
        case 'Manager':
            $dashboardLink = 'manager.php';
            break;
        case 'Employee':
            $dashboardLink = 'employee.php';
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = $_POST['otp'];
    $newPassword = $_POST['new_password'];

    if ($enteredOtp == $_SESSION['otp']) {
        $email = $_SESSION['email'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in DB
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        $message = "<div class='success'>✅ Password reset successful for <strong>$email</strong>!</div>";
        session_destroy();
    } else {
        $message = "<div class='error'>❌ Invalid OTP!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Step 2</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6B73FF, #000DFF);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            color: #000DFF;
        }

        input[type="text"], input[type="password"], button {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #000DFF;
            color: white;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #3344ff;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .back-btn {
            background-color: gray;
            margin-top: 10px;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            display: inline-block;
        }

        .back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Your Password</h2>
    <form method="POST" action="">
        <input type="text" name="otp" required placeholder="Enter OTP">
        <input type="password" name="new_password" required placeholder="Enter New Password">
        <button type="submit">Reset Password</button>
    </form>

    <?php
    if (!empty($message)) {
        echo $message;
    }
    ?>

    <a class="back-btn" href="<?= $dashboardLink ?>">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
