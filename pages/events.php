<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

global $pdo;
try {
    $stmt = $pdo->query("SELECT events.*, users.name AS user_name FROM events JOIN users ON events.user_id = users.id ORDER BY events.created_at DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching events: " . $e->getMessage());
    $events = [];
    $error = "Failed to load events.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    if ($event_id && makeReservation($_SESSION['user_id'], $event_id)) {
        $success = "Reservation made successfully.";
    } else {
        $error = "Failed to make reservation.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
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
            background: url('background.jpg') no-repeat center center fixed;
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
        .events-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .events-container h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .event-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .event-card:hover {
            transform: scale(1.05);
        }
        .event-card h3 {
            margin-bottom: 0.5rem;
            color: #fbc531;
        }
        .event-card img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .more-info-btn {
            background: #fbc531;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .more-info-btn:hover {
            background: #e0a829;
        }
        .event-card form button {
            width: 100%;
            padding: 0.75rem;
            background: #1e3c72;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 1rem;
            transition: background 0.3s, transform 0.2s;
        }
        .event-card form button:hover {
            background: #fbc531;
            color: #333;
            transform: scale(1.04);
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
    <div class="events-container">
        <h2>All Events</h2>
        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                    <p><strong>Place:</strong> <?php echo htmlspecialchars($event['place']); ?></p>
                    <?php if (!empty($event['photo'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($event['photo']); ?>" alt="Event Photo">
                    <?php endif; ?>
                    <button class="more-info-btn" onclick="toggleDetails(<?php echo $event['id']; ?>)">More Information</button>
                    <div class="event-details" id="details-<?php echo $event['id']; ?>" style="display: none;">
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($event['capacity']); ?></p>
                        <p><strong>Created by:</strong> <?php echo htmlspecialchars($event['user_name']); ?></p>
                        <p><strong>Created at:</strong> <?php echo htmlspecialchars($event['created_at']); ?></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit">Reserve</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer id="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h2>Tirana Unplugged</h2>
                <p>Dont hesitate to join our events!</p>
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
        function toggleDetails(eventId) {
            const details = document.getElementById(`details-${eventId}`);
            if (details.style.display === "none") {
                details.style.display = "block";
            } else {
                details.style.display = "none";
            }
        }

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

