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
    <title>Events</title>
    <link rel="stylesheet" href="../assets/css/events.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <script src="../assets/js/script.js"></script>
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
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <div class="events-grid">
            <?php if (empty($events)): ?>
                <p>No events available.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                        <p><strong>Place:</strong> <?php echo htmlspecialchars($event['place']); ?></p>
                        <?php if ($event['photo']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($event['photo']); ?>" alt="Event Photo">
                        <?php endif; ?>
                        <button class="more-info-btn" onclick="toggleDetails(<?php echo $event['id']; ?>)">More Information</button>
                        <div class="event-details" id="details-<?php echo $event['id']; ?>" style="display: none;">
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
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
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

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
