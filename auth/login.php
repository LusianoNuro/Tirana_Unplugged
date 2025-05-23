<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Login form submission
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if (loginUser($email, $password)) {
            redirect('../pages/home.php');
        } else {
            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error) echo "<p class=\"error\">$error</p>"; ?>
        <form id="login-form" method="POST" style="display: flex; flex-direction: column;">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
            <a href="forgot_password.php" style="margin-top: 10px; color: #007bff; cursor: pointer; display: inline-block; text-decoration: underline;">Forget your password?</a>
        </form>

        <p>Don't have an account? <a href="signup.php">Signup</a></p>
    </div>
</body>
</html>
