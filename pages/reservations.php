<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

global $pdo;
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT r.*, e.name AS event_name FROM reservations r JOIN events e ON r.event_id = e.id WHERE r.user_id = ?");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
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
        .reservations-container {
            max-width: 800px;
            margin: 5rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .reservations-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .reservation-item {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .reservation-item h3 {
            margin-bottom: 0.5rem;
            color: #fbc531;
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
    <div class="reservations-container">
        <h2>My Reservations</h2>
        <?php if (empty($reservations)): ?>
            <p>You have no reservations yet.</p>
        <?php else: ?>
            <?php foreach ($reservations as $reservation): ?>
                <div class="reservation-item">
                    <h3><?php echo htmlspecialchars($reservation['event_name']); ?></h3>
                    <p>Reservation Date: <?php echo isset($reservation['reservation_date']) ? htmlspecialchars($reservation['reservation_date']) : htmlspecialchars($reservation['created_at']); ?></p>
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


