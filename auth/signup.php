<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    
    if (registerUser($name, $surname, $email, $phone, $password, $birthdate, $gender)) {
        redirect('login.php');
    } else {
        $error = "Registration failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
</head>
<body>
    <div class="signup-container">
        <h2>Signup</h2>
        <?php if (isset($error)) echo "<p class=\"error\">$error</p>"; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required><br>
            <input type="text" name="surname" placeholder="Surname" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="tel" name="phone" placeholder="Phone"><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="date" name="birthdate" placeholder="Birthdate"><br>
            <select name="gender">
                <option value="">Select Gender</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select><br>
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>