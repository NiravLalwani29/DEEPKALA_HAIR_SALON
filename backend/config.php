<?php
// Starts the session on every page that includes this file
session_start();

// Basic MySQL connection settings — change if your XAMPP setup is different
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "deepkala";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// Stylist holidays (admin marks them; booking + reschedule skip these days). Created here so no manual import is needed.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS barber_leaves (     barber_id  INT  NOT NULL,     leave_date DATE NOT NULL,     PRIMARY KEY (barber_id, leave_date) )");
?>
