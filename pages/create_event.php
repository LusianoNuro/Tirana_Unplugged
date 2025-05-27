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

    // Debug information
    error_log("Form submitted with user_id: " . ($user_id ?? 'null'));
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));

    // Validate inputs
    if (empty($name) || empty($date) || empty($place) || $capacity === false || empty($description) || !$user_id) {
        $error = "All fields are required, and capacity must be a valid number.";
        error_log("Validation failed: name=$name, date=$date, place=$place, capacity=$capacity, description=$description, user_id=$user_id");
    } else {
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            // Create uploads directory if it doesn't exist
            $target_dir = __DIR__ . "/../uploads/";
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    $error = "Failed to create uploads directory. Please check permissions.";
                    error_log("Failed to create uploads directory: " . $target_dir);
                } else {
                    error_log("Created uploads directory: " . $target_dir);
                }
            }
            // Double-check directory is writable
            if (!is_writable($target_dir)) {
                $error = "Uploads directory is not writable. Please check permissions.";
                error_log("Uploads directory is not writable: " . $target_dir);
            }
            if (!$error) {
                // Generate unique filename to prevent conflicts
                $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $photo = uniqid() . '_' . time() . '.' . $file_extension;
                $target_file = $target_dir . $photo;
                error_log("Attempting to upload file to: " . $target_file);
                // Validate image
                $check = getimagesize($_FILES['photo']['tmp_name']);
                if ($check === false) {
                    $error = "File is not an image.";
                    error_log("File validation failed: not an image");
                } elseif ($_FILES['photo']['size'] > 5000000) { // 5MB limit
                    $error = "File is too large. Maximum size is 5MB.";
                    error_log("File validation failed: too large (" . $_FILES['photo']['size'] . " bytes)");
                } elseif (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
                    error_log("File validation failed: invalid extension ($file_extension)");
                } elseif (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $error = "Failed to upload photo. Please check directory permissions and file size limits.";
                    error_log("File upload failed: could not move file from " . $_FILES['photo']['tmp_name'] . " to " . $target_file);
                } else {
                    error_log("File uploaded successfully: " . $target_file);
                }
            }
        } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE) {
            // Handle upload errors
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'File is too large (server limit)',
                UPLOAD_ERR_FORM_SIZE => 'File is too large (form limit)',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
                UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
                UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
            ];
            $error = $upload_errors[$_FILES['photo']['error']] ?? 'Unknown upload error';
            error_log("Upload error: " . $error . " (code: " . $_FILES['photo']['error'] . ")");
        }

        // Create event if no errors
        if (!$error) {
            try {
                error_log("Attempting to create event with photo: " . $photo);
                if (createEvent($name, $date, $place, $capacity, $description, $photo, $user_id)) {
                    $success = "Event created successfully!";
                    error_log("Event created successfully");
                    // Clear form data after successful creation
                    $_POST = [];
                } else {
                    $error = "Failed to create event. Please try again.";
                    error_log("createEvent function returned false");
                }
            } catch (Exception $e) {
                $error = "Error creating event: " . $e->getMessage();
                error_log("Exception in createEvent: " . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
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
            background: url('https://images.pexels.com/photos/15016180/pexels-photo-15016180/free-photo-of-people-on-rock-concert.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed; /* Vendosni rrugën e saktë të imazhit */
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
        .create-event-container {
            max-width: 600px;
            margin: 5rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .create-event-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .create-event-container input,
        .create-event-container textarea {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .create-event-container button {
            width: 100%;
            padding: 0.75rem;
            background: #fbc531;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .create-event-container button:hover {
            background: #e0a829;
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
        
        /* Alert styles */
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Photo upload styles */
        .photo-upload-section {
            margin-bottom: 1rem;
        }
        
        .photo-upload-section label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .image-preview {
            margin-top: 1rem;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
          .create-event-container {
            padding: 1rem;
            margin: 2rem 0.5rem;
          }
        }
        @media (max-width: 500px) {
          .create-event-container {
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
    <div class="create-event-container">
        <h2>Create Event</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" id="eventForm">
            <input type="text" name="name" placeholder="Event Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <input type="datetime-local" name="date" required value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">
            <input type="text" name="place" placeholder="Place" required value="<?php echo isset($_POST['place']) ? htmlspecialchars($_POST['place']) : ''; ?>">
            <input type="number" name="capacity" placeholder="Capacity" required value="<?php echo isset($_POST['capacity']) ? htmlspecialchars($_POST['capacity']) : ''; ?>">
            <textarea name="description" placeholder="Description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            
            <div class="photo-upload-section">
                <label for="photo">Event Photo:</label>
                <input type="file" name="photo" id="photo" accept="image/*">
                <div id="imagePreview" class="image-preview"></div>
            </div>
            
            <button type="submit">Create Event</button>
        </form>
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
            
            // Image preview functionality
            const photoInput = document.getElementById('photo');
            const imagePreview = document.getElementById('imagePreview');
            
            if (photoInput && imagePreview) {
                photoInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    
                    // Clear previous preview
                    imagePreview.innerHTML = '';
                    
                    if (file) {
                        // Validate file type
                        if (!file.type.startsWith('image/')) {
                            imagePreview.innerHTML = '<p style="color: #721c24;">Please select a valid image file.</p>';
                            return;
                        }
                        
                        // Validate file size (5MB limit)
                        if (file.size > 5000000) {
                            imagePreview.innerHTML = '<p style="color: #721c24;">File size must be less than 5MB.</p>';
                            return;
                        }
                        
                        // Create and display preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Image Preview';
                            imagePreview.appendChild(img);
                            
                            const fileName = document.createElement('p');
                            fileName.textContent = 'Selected: ' + file.name;
                            fileName.style.marginTop = '0.5rem';
                            fileName.style.fontSize = '0.9rem';
                            fileName.style.color = '#666';
                            imagePreview.appendChild(fileName);
                        };
                        reader.readAsDataURL(file);
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
