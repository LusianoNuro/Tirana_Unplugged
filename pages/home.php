<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="../assets/css/home.css">
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
    <div class="home-container">
        <h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>
        <p>This is the home page of the Event Management System. Explore events, create your own, or manage your reservations.</p>
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
