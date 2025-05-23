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
    <title>My Reservations</title>
    <link rel="stylesheet" href="../assets/css/reservations.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
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
        <?php foreach ($reservations as $reservation): ?>
            <div>
                <h3><?php echo $reservation['event_name']; ?></h3>
                <p>Reservation Date: <?php echo $reservation['reservation_date']; ?></p>
            </div>
        <?php endforeach; ?>
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
