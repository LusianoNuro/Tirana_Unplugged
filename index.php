<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('pages/home.php');
} else {
    redirect('auth/login.php');
}
?>