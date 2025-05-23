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
            $error = "Password updated successfully. You can now login.";
            $showNewPasswordForm = false;
            unset($_SESSION['verification_code'], $_SESSION['reset_email'], $_SESSION['code_expiry']);
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
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Forgot Password</h2>
        <?php if ($error) echo "<p class=\"error\">$error</p>"; ?>
        <form id="forget-password-form" method="POST" style="display: <?php echo (!$showVerificationForm && !$showNewPasswordForm) ? 'flex' : 'none'; ?>; flex-direction: column; margin-top: 20px;">
            <input type="email" name="reset_email" placeholder="Enter your email" required><br>
            <button type="submit">Reset Password</button>
            <a href="login.php" style="margin-top: 10px; display: inline-block;">Back to Login</a>
        </form>

        <form id="verification-form" method="POST" style="display: <?php echo $showVerificationForm ? 'flex' : 'none'; ?>; flex-direction: column; margin-top: 20px;">
            <input type="text" name="verification_code" placeholder="Enter verification code" required><br>
            <button type="submit">Verify Code</button>
            <a href="login.php" style="margin-top: 10px; display: inline-block;">Back to Login</a>
        </form>

        <form id="new-password-form" method="POST" style="display: <?php echo $showNewPasswordForm ? 'flex' : 'none'; ?>; flex-direction: column; margin-top: 20px;">
            <input type="password" name="new_password" placeholder="Enter new password" required><br>
            <button type="submit">Update Password</button>
            <a href="login.php" style="margin-top: 10px; display: inline-block;">Back to Login</a>
        </form>
    </div>
</body>
</html>
