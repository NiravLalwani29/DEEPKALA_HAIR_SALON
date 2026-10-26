<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "deepkala";

define('MAIL_URL', 'https://script.google.com/macros/s/AKfycbwlmNhhhsGDgqOu9IwoS-ZqhuxhZJ0sU8dEDxAKNhb0gUtdSfBYajnbJozUr34x2xqk6Q/exec');
define('MAIL_KEY', 'dkSalon_7xQ92mVpL4tR8');
define('SMTP_USER', 'deepkalahairsalon@gmail.com');
define('SMTP_PASS', 'tvmz bkjq nitw bowi');

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

function q($c, $sql, ...$args) {
    $st = mysqli_prepare($c, $sql);
    if ($args) mysqli_stmt_bind_param($st, str_repeat('s', count($args)), ...$args);
    mysqli_stmt_execute($st);
    return $st;
}
function row($st) { return mysqli_fetch_assoc(mysqli_stmt_get_result($st)); }
