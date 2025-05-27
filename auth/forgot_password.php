<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = '';
$showNewPasswordForm = false;
$resetEmail = '';
$showVerificationForm = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset_email']) && !isset($_POST['verification_code']) && !isset($_POST['new_password'])) {
        // Forget password email submission - generate and send verification code
        $resetEmail = $_POST['reset_email'];
        if (checkEmailExists($resetEmail)) {
            $verificationCode = rand(100000, 999999);
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['reset_email'] = $resetEmail;
            $_SESSION['code_expiry'] = time() + 900; // 15 minutes expiry

            // Send verification code email from a non-existent email address
            require_once 'phpMailer.php';
            if (!sendVerificationEmail($resetEmail, $verificationCode)) {
                $error = "Verification email could not be sent. Please try again later.";
            } else {
                $showVerificationForm = true;
            }
        } else {
            $error = "Email not found.";
        }
    } elseif (isset($_POST['verification_code']) && !isset($_POST['new_password'])) {
        // Verify the code
        $resetEmail = $_SESSION['reset_email'] ?? '';
        $codeExpiry = $_SESSION['code_expiry'] ?? 0;
        $inputCode = $_POST['verification_code'];

        if (time() > $codeExpiry) {
            $error = "Verification code expired. Please try again.";
            $showVerificationForm = false;
            $showNewPasswordForm = false;
            unset($_SESSION['verification_code'], $_SESSION['reset_email'], $_SESSION['code_expiry']);
        } elseif ($inputCode == $_SESSION['verification_code']) {
            $showNewPasswordForm = true;
            $showVerificationForm = false;
        } else {
            $error = "Invalid verification code.";
            $showVerificationForm = true;
        }
    } elseif (isset($_POST['new_password']) && isset($_SESSION['reset_email'])) {
        // New password submission
        $resetEmail = $_SESSION['reset_email'];
        $newPassword = $_POST['new_password'];
        if (updatePassword($resetEmail, $newPassword)) {
            // Redirect to login after successful password update
            unset($_SESSION['verification_code'], $_SESSION['reset_email'], $_SESSION['code_expiry']);
            header('Location: login.php?reset=success');
            exit();
        } else {
            $error = "Failed to update password. Please try again.";
            $showNewPasswordForm = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Forgot Password</title>
    <style>
        body {
            background-image: url('https://albaniatourguide.com/wp-content/uploads/2022/01/Tirana-12-shutterstock-large-1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 80px;
        }

        .login-container {
            background-color: rgba(245, 248, 178, 0.85);
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 24px;
            color: #333;
            font-weight: 600;
        }

        .error {
            color: #d8000c;
            background: #ffbaba;
            border: 1px solid #d8000c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
            font-weight: 500;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-top: 20px;
            gap: 15px;
            border: 3px solid #7c3a0e;
            border-radius: 8px;
            padding: 20px;
            background-color: #fafafa;
            box-sizing: border-box;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"] {
            padding: 12px 15px;
            border: 2px solid #ef4911;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #74873b;
            box-shadow: 0 0 5px rgba(234, 190, 14, 0.6);
        }

        button {
            background-color: #ff3300;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            padding: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #883a13;
        }

        a {
            color: #d13409;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
            font-size: 14px;
            text-align: center;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Forgot Password</h2>
        <?php if ($error) echo "<p class=\"error\">$error</p>"; ?>
        <form id="forget-password-form" method="POST" style="display: <?php echo (!$showVerificationForm && !$showNewPasswordForm) ? 'flex' : 'none'; ?>; flex-direction: column; margin-top: 20px;">
            <input type="email" name="reset_email" placeholder="Enter your email" required />
            <button type="submit">Reset Password</button>
            <a href="login.php">Back to Login</a>
        </form>
        <form id="verification-form" method="POST" style="display: <?php echo $showVerificationForm ? 'flex' : 'none'; ?>; flex-direction: column; margin-top: 20px;">
            <input type="text" name="verification_code" placeholder="Enter verification code" required />
            <button type="submit">Verify Code</button>
            <a href="login.php">Back to Login</a>
        </form>
        <form id="new-password-form" method="POST" style="display: <?php echo $showNewPasswordForm ? 'flex' : 'none'; ?>; flex-direction: column; margin-top: 20px;">
            <input type="password" name="new_password" placeholder="Enter new password" required />
            <button type="submit">Update Password</button>
            <a href="login.php">Back to Login</a>
        </form>
    </div>
</body>
</html>
