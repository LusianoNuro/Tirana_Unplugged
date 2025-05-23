<?php
require_once 'db_connect.php';

function registerUser($name, $surname, $email, $phone, $password, $birthdate, $gender, $is_admin = 0) {
    global $pdo;
    try {
        echo $gender;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, surname, email, phone, password, birthdate, gender, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $surname, $email, $phone, $hashed_password, $birthdate, $gender, $is_admin]);
        return $result;
    } catch (PDOException $e) {
        echo $e->getMessage();
        error_log("Error in registerUser: " . $e->getMessage());
        return false;
    }
}

function loginUser($email, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error in loginUser: " . $e->getMessage());
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function createEvent($name, $date, $place, $capacity, $description, $photo, $user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO events (name, date, place, capacity, description, photo, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $date, $place, $capacity, $description, $photo, $user_id]);
    } catch (PDOException $e) {
        error_log("Error in createEvent: " . $e->getMessage());
        throw $e; // Rethrow to catch in calling code
    }
}

function makeReservation($user_id, $event_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $event_id]);
    } catch (PDOException $e) {
        error_log("Error in makeReservation: " . $e->getMessage());
        return false;
    }
}

function submitSupportTicket($user_id, $problem_title, $problem_description) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO support (user_id, problem_title, problem_description) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $problem_title, $problem_description]);
    } catch (PDOException $e) {
        error_log("Error in submitSupportTicket: " . $e->getMessage());
        return false;
    }
}

function checkEmailExists($email) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    } catch (PDOException $e) {
        error_log("Error in checkEmailExists: " . $e->getMessage());
        return false;
    }
}

function updatePassword($email, $newPassword) {
    global $pdo;
    try {
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        return $stmt->execute([$hashed_password, $email]);
    } catch (PDOException $e) {
        error_log("Error in updatePassword: " . $e->getMessage());
        return false;
    }
}

function generateToken($length = 50) {
    return bin2hex(random_bytes($length));
}

function sendPasswordResetEmail($email, $token) {
    $resetLink = "http://localhost/projekt_web/auth/reset_password.php?token=" . urlencode($token);
    $subject = "Password Reset Request";
    $message = "You requested a password reset. Click the link below to reset your password:\n\n" . $resetLink . "\n\nIf you did not request this, please ignore this email.";
    $headers = "From: no-reply@yourdomain.com\r\n";
    return mail($email, $subject, $message, $headers);
}

function storePasswordResetToken($email, $token, $expiresAt) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $token, $expiresAt]);
    } catch (PDOException $e) {
        error_log("Error in storePasswordResetToken: " . $e->getMessage());
        return false;
    }
}

function verifyPasswordResetToken($token) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['email'] : false;
    } catch (PDOException $e) {
        error_log("Error in verifyPasswordResetToken: " . $e->getMessage());
        return false;
    }
}

function deletePasswordResetToken($token) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        return $stmt->execute([$token]);
    } catch (PDOException $e) {
        error_log("Error in deletePasswordResetToken: " . $e->getMessage());
        return false;
    }
}
?>
