<?php
session_start();
$otpMessage = "";

// Optional: Hardcoded or role-based dashboard redirection
$dashboardLink = 'dashboard.php'; // default
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Generate a 4-digit OTP
    $otp = rand(1000, 9999);

    // Store OTP and email in session
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    $otpMessage = "<div class='otp-box'><p>OTP generated for <strong>$email</strong>:</p><h2>$otp</h2><a href='verify_otp.php'>Click here to verify OTP</a></div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Step 1</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6B73FF, rgb(151, 221, 249));
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

        input[type="email"], button {
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
            background-color: rgba(70, 170, 217, 0.83);
        }

        .otp-box {
            background-color: #f1f1f1;
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            color: #000;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: rgb(104, 107, 181);
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-btn {
            background-color:light blue ;
            margin-top: 10px;
        }

        .back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST" action="">
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit">Generate OTP</button>
    </form>

    <?php
    if (!empty($otpMessage)) {
        echo $otpMessage;
    }
    ?>

    <a href="<?= $dashboardLink ?>"><button class="back-btn">⬅️ Back to Dashboard</button></a>
</div>

</body>
</html>
