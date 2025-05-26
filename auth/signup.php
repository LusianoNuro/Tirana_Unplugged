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
    <meta charset="UTF-8" />
    <title>Signup</title>
    <style>
        /* Reset box-sizing to include border in size */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('https://www.travelguide.net/media/tirana.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
            color: #c11068;
        }

        .signup-container {
            background: rgba(198, 225, 228, 0.767);
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(13, 0, 0, 0.974);
            width: 550px;
            text-align: center;
            border: 5px solid #cdcd0e;

            /* scale down for larger width but fits well */
            transform: scale(0.85);
            transform-origin: center center;

            /* disable scrolling, keep fully visible */
            max-height: none;
            overflow: visible;
        }

        .signup-container h2 {
            margin-bottom: 30px;
            font-weight: 700;
            color: #090909;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        input[type="date"],
        select {
            padding: 12px 15px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 3.5px solid #cc9e00;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #e6c200;
            outline: none;
        }

        button {
            background-color: #c2f00c;
            color: #fff;
            padding: 14px 20px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #ab38900;
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
        }

        p a {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Signup</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required />
            <input type="text" name="surname" placeholder="Surname" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="tel" name="phone" placeholder="Phone" />
            <input type="password" name="password" placeholder="Password" required />
            <input type="date" name="birthdate" placeholder="Birthdate" />
            <select name="gender">
                <option value="">Select Gender</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
