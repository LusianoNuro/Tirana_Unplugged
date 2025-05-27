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
    <style>
        /* Background image styling */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('https://images.pexels.com/photos/32216558/pexels-photo-32216558/free-photo-of-yellow-vintage-van-in-the-streets-of-tirana.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            position: relative;
            overflow: hidden;
        }

        /* Login container styling with transparent yellow */
        .login-container {
            background: rgba(119, 223, 186, 0.85); /* soft yellow with transparency */
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            width: 320px;
            text-align: center;
            color: #222;
            border: 5px solid #cdcd0e; /* thicker border */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .login-container h2 {
            margin-bottom: 30px;
            font-weight: 700;
            color: #171302; /* a darker yellow-brown for contrast */
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px 15px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 2.5px solid #cc9e00; /* thicker border */
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #e6c200;
            outline: none;
        }

        button {
            background-color: #c2f00c;
            color: #4a3c00;
            padding: 14px 20px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b38900;
        }

        a {
            color: #e70f0fdf;
            text-decoration: underline;
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
        }

        p {
            margin-top: 25px;
            font-size: 14px;
            color: #aa075b;
        }

        p a {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div style="color: #b30000; background: #ffeaea; border: 1.5px solid #b30000; padding: 10px 0; border-radius: 6px; margin-bottom: 18px; font-weight: 600;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form id="login-form" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <a href="forgot_password.php">Forget your password?</a>
        </form>

        <p>Don't have an account? <a href="signup.php">Signup</a></p>
    </div>
    <script>
        // Show popup ONLY if redirected after password reset and not on login error
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            // Only show popup if there is no error message on the page
            const hasError = document.querySelector('.login-container div[style*="background: #ffeaea"]');
            if (params.get('reset') === 'success' && !hasError) {
                const popup = document.createElement('div');
                popup.textContent = 'Your password has been updated!';
                popup.style.position = 'fixed';
                popup.style.top = '30px';
                popup.style.left = '50%';
                popup.style.transform = 'translateX(-50%)';
                popup.style.background = '#27ae60';
                popup.style.color = '#fff';
                popup.style.padding = '18px 32px';
                popup.style.borderRadius = '8px';
                popup.style.fontWeight = 'bold';
                popup.style.fontSize = '1.2rem';
                popup.style.boxShadow = '0 4px 18px rgba(39,174,96,0.18)';
                popup.style.zIndex = '9999';
                document.body.appendChild(popup);
                setTimeout(() => popup.remove(), 3500);
            }
        });
    </script>
</body>
</html>


