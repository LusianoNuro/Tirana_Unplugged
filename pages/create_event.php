<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $place = trim($_POST['place'] ?? '');
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    $description = trim($_POST['description'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;
    $photo = '';

    // Validate inputs
    if (empty($name) || empty($date) || empty($place) || $capacity === false || empty($description) || !$user_id) {
        $error = "All fields are required, and capacity must be a valid number.";
    } else {
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            // Use absolute path for uploads directory
            $target_dir = __DIR__ . "/../uploads/";
            $photo = basename($_FILES['photo']['name']);
            $target_file = $target_dir . $photo;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate image
            $check = getimagesize($_FILES['photo']['tmp_name']);
            if ($check === false) {
                $error = "File is not an image.";
            } elseif ($_FILES['photo']['size'] > 5000000) { // 5MB limit
                $error = "File is too large.";
            } elseif (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $error = "Failed to upload photo. Please check directory permissions and file path.";
            }
        } elseif (!empty($_POST['existing_photo'])) {
            // Preserve existing photo if no new photo uploaded
            $photo = $_POST['existing_photo'];
        }

        // Create event if no errors
if (!$error) {
    try {
        if (createEvent($name, $date, $place, $capacity, $description, $photo, $user_id)) {
            $success = "Event created successfully.";
            // Removed redirect to show photo preview on the same page
            // header("Refresh: 2; url=events.php");
        } else {
            $error = "Failed to create event. Please try again.";
        }
    } catch (Exception $e) {
        $error = "Error creating event: " . $e->getMessage();
    }
}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <link rel="stylesheet" href="../assets/css/create_event.css">
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
    <div class="create-event-container">
        <h2>Create Event</h2>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Event Name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required><br>
            <input type="datetime-local" name="date" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>" required><br>
            <input type="text" name="place" placeholder="Place" value="<?php echo htmlspecialchars($_POST['place'] ?? ''); ?>" required><br>
            <input type="number" name="capacity" placeholder="Capacity" value="<?php echo htmlspecialchars($_POST['capacity'] ?? ''); ?>" required><br>
            <textarea name="description" placeholder="Description" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea><br>
            <input type="file" name="photo" accept="image/*"><br>
            <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($photo); ?>">
            <?php if ($photo): ?>
                <p>Uploaded Photo Preview:</p>
                <img src="../uploads/<?php echo htmlspecialchars($photo); ?>" alt="Uploaded Photo" style="max-width: 300px; max-height: 300px;">
            <?php endif; ?>
            <button type="submit">Create Event</button>
        </form>
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
