<?php
// Starts the session on every page that includes this file
session_start();

// Basic MySQL connection settings — change if your XAMPP setup is different
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "deepkala";

// Gmail SMTP for forgot-password OTP mails: use a Gmail "App Password" (16 chars, no spaces). Empty = OTP only goes to the PHP error log.
// Google Apps Script mail relay (see setup steps): paste the Web app URL + your secret key. Empty = fall back to direct SMTP below.
define('MAIL_URL', 'https://script.google.com/macros/s/AKfycbwlmNhhhsGDgqOu9IwoS-ZqhuxhZJ0sU8dEDxAKNhb0gUtdSfBYajnbJozUr34x2xqk6Q/exec');
define('MAIL_KEY', 'dkSalon_7xQ92mVpL4tR8');
define('SMTP_USER', 'deepkalahairsalon@gmail.com');
define('SMTP_PASS', 'tvmz bkjq nitw bowi');

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
