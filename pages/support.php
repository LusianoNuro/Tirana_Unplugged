<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $problem_title = $_POST['problem_title'];
    $problem_description = $_POST['problem_description'];
    $user_id = $_SESSION['user_id'];
    
    if (submitSupportTicket($user_id, $problem_title, $problem_description)) {
        $success = "Support ticket submitted successfully.";
    } else {
        $error = "Failed to submit support ticket.";
    }
}

global $pdo;
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM support WHERE user_id = ?");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
    <style>
        /* Reset and base styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background: url('background.jpg') no-repeat center center fixed; /* Vendosni rrugën e saktë të imazhit */
            background-size: cover;
        }
        nav.navbar {
            background: rgba(30, 60, 114, 0.85);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        nav.navbar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        nav.navbar a {
            color: #fbc531;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            transition: color 0.3s;
        }
        nav.navbar a:hover {
            color: #fff;
        }
        .support-container {
            max-width: 800px;
            margin: 5rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .support-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .support-container h3 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #fbc531;
        }
        .support-container input,
        .support-container textarea {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .support-container button {
            width: 100%;
            padding: 0.75rem;
            background: #fbc531;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .support-container button:hover {
            background: #e0a829;
        }
        .ticket-item {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f9f9f9;
        }
        footer#footer {
            background: rgba(30, 60, 114, 0.9);
            color: #eee;
            padding: 2rem 1rem;
            margin-top: 4rem;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
        }
        .footer-section {
            flex: 1 1 250px;
            min-width: 220px;
        }
        .footer-section h2, .footer-section h3 {
            color: #fbc531;
            margin-bottom: 0.75rem;
        }
        .footer-bottom {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #bbb;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="reservations.php">My Reservations</a></li>
            <li><a href="support.php">Support</a></li>
            <li><a href="#footer">About Us</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="support-container">
        <h2>Support</h2>
        <h3>Submit your problem</h3>
        <form method="POST">
            <input type="text" name="problem_title" placeholder="Problem Title" required>
            <textarea name="problem_description" placeholder="Problem Description" required></textarea>
            <button type="submit">Submit </button>
        </form>
        <h3>Your problems before: </h3>
        <?php if (empty($tickets)): ?>
            <p>You have not submitted any support tickets yet.</p>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-item">
                    <h4><?php echo htmlspecialchars($ticket['problem_title']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($ticket['problem_description'])); ?></p>
                    <p>Submitted: <?php echo htmlspecialchars($ticket['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <footer id="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h2>Tirana Unplugged</h2>
                <p>Tirana Unplugged është një platformë online që mundëson përdoruesve të rezervojnë dhe krijojnë evente në qytetin e Tiranës. Me një dizajn të thjeshtë dhe intuitiv, përdoruesit mund të eksplorojnë mundësitë për ngjarje të ndryshme dhe të organizojnë aktivitetet e tyre të preferuara.</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>+355 69 558 6969</p>
                <p>makinaime@gmail.com</p>
                <p>Rruga e Elbasanit, Tirana, Albania</p>
            </div>
            <div class="footer-section">
                <h3>Social Media</h3>
                <p>TikTok</p>
                <p>Instagram</p>
                <p>Facebook</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Copyright 2025. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aboutUsLink = document.querySelector('a[href="#footer"]');
            if (aboutUsLink) {
                aboutUsLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    const footer = document.getElementById('footer');
                    if (footer) {
                        footer.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }
        });
    </script>
</body>
</html>

