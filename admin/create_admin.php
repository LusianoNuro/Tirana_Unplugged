<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$name = 'Lusiano';
$surname = 'Nuro';
$email = 'lusinuro@yahoo.com';
$phone = '+3556945231024';
$password = 'lusi123'; // Change to a secure password
$birthdate = '1980-01-01';
$gender = 'M';
$is_admin = 1;

if (registerUser($name, $surname, $email, $phone, $password, $birthdate, $gender, $is_admin)) {
    echo "Admin created successfully.";
} else {
    echo "Failed to create admin.";
}
?>