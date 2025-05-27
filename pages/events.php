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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['event_id']) && isset($_POST['update_event'])) {
        // Handle event update by admin
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
            $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
            $name = trim($_POST['name'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $place = trim($_POST['place'] ?? '');
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
            $description = trim($_POST['description'] ?? '');
            if ($event_id && $name && $date && $place && $capacity && $description) {
                $stmt = $pdo->prepare("UPDATE events SET name=?, date=?, place=?, capacity=?, description=? WHERE id=?");
                if ($stmt->execute([$name, $date, $place, $capacity, $description, $event_id])) {
                    $success = "Event updated successfully.";
                } else {
                    $error = "Failed to update event.";
                }
            } else {
                $error = "All fields are required.";
            }
        }
    } elseif (isset($_POST['event_id'])) {
        $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($event_id && makeReservation($_SESSION['user_id'], $event_id)) {
            $success = "Reservation made successfully.";
        } else {
            $error = "Failed to make reservation.";
        }
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
            background: url('data:image/jpeg;base64,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') no-repeat center center fixed;
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
        /* Make the Edit button green */
        .event-card form[action=""], .event-card form[style*="margin-top"] button[type="submit"] {
            background: #27ae60 !important;
            color: #fff !important;
        }
        .event-card form[style*="margin-top"] button[type="submit"]:hover {
            background: #219150 !important;
            color: #fff !important;
        }
        /* Footer styles from create_event.php */
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

        /* Responsive Navbar for Mobile */
        @media (max-width: 700px) {
          nav.navbar {
            padding: 0.7rem 0.5rem;
          }
          nav.navbar ul {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
            background: rgba(30, 60, 114, 0.97);
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            padding: 0.5rem 1rem;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            display: none;
            z-index: 200;
          }
          nav.navbar ul.active {
            display: flex;
          }
          nav.navbar .navbar-toggle {
            display: block;
            background: none;
            border: none;
            color: #fbc531;
            font-size: 2rem;
            position: absolute;
            right: 1.2rem;
            top: 1rem;
            z-index: 201;
            cursor: pointer;
          }
          nav.navbar ul li {
            width: 100%;
          }
          nav.navbar ul li a {
            display: block;
            width: 100%;
            padding: 0.7rem 0;
            border-bottom: 1px solid #fbc53122;
          }
          nav.navbar ul li:last-child a {
            border-bottom: none;
          }
        }
        @media (min-width: 701px) {
          nav.navbar .navbar-toggle {
            display: none !important;
          }
        }
        @media (max-width: 600px) {
          .events-container {
            padding: 1rem;
            margin: 2rem 0.5rem;
          }
          .events-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
          }
        }
        @media (max-width: 500px) {
          .events-container {
            padding: 0.5rem;
            margin: 1rem 0.2rem;
          }
        }
        /* Responsive Footer */
        @media (max-width: 700px) {
          .footer-container {
            flex-direction: column;
            gap: 1.5rem;
            padding: 0 0.5rem;
          }
          .footer-section {
            min-width: 0;
          }
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
        <button class="navbar-toggle" aria-label="Toggle navigation">
          &#9776;
        </button>
    </nav>
    <div class="events-container">
        <h2>All Events</h2>
        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1 && isset($_POST['edit_event_id']) && $_POST['edit_event_id'] == $event['id']): ?>
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <input type="hidden" name="update_event" value="1">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
                            <input type="datetime-local" name="date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['date'])); ?>" required>
                            <input type="text" name="place" value="<?php echo htmlspecialchars($event['place']); ?>" required>
                            <input type="number" name="capacity" value="<?php echo htmlspecialchars($event['capacity']); ?>" required>
                            <textarea name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                            <button type="submit">Save</button>
                            <button type="button" onclick="window.location.href='events.php'">Cancel</button>
                        </form>
                    <?php else: ?>
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
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <form method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="edit_event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit">Edit</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer id="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h2>Tirana Unplugged</h2>
                <p>Tirana Unplugged is an online platform that enables users to book and create events in the city of Tirana. With a simple and intuitive design, users can explore opportunities for various events and organize their favorite activities.</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><a href="tel:+355695586969" style="color: #fbc531; text-decoration: underline;">+355 69 558 6969</a></p>
                <p><a href="mailto:tiranaUnplugged@gmail.com" style="color: #fbc531; text-decoration: underline;">tiranaUnplugged@gmail.com</a></p>
                <p>Elbasani Street, Tirana, Albania</p>
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

            // Navbar toggle for mobile
            const navbarToggle = document.querySelector('.navbar-toggle');
            const navbarMenu = document.querySelector('nav.navbar ul');
            if (navbarToggle && navbarMenu) {
                navbarToggle.addEventListener('click', function() {
                    navbarMenu.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>

