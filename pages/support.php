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
    <title>Support</title>
    <link rel="stylesheet" href="../assets/css/support.css">
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
    <div class="support-container">
        <h2>Support</h2>
        <?php if (isset($success)) echo "<p>$success</p>"; ?>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <h3>Submit a Support Ticket</h3>
        <form method="POST">
            <input type="text" name="problem_title" placeholder="Problem Title" required><br>
            <textarea name="problem_description" placeholder="Problem Description" required></textarea><br>
            <button type="submit">Submit Ticket</button>
        </form>
        <h3>Your Support Tickets</h3>
        <?php foreach ($tickets as $ticket): ?>
            <div>
                <h4><?php echo $ticket['problem_title']; ?></h4>
                <p><?php echo $ticket['problem_description']; ?></p>
                <p>Submitted: <?php echo $ticket['submitted_at']; ?></p>
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
