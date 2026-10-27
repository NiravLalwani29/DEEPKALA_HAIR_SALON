<?php
require 'config.php';
require 'mail.php';
header('Content-Type: application/json');

function done($ok, $msg, $extra = []) { echo json_encode(['ok' => $ok, 'msg' => $msg] + $extra); exit; }

if (empty($_SESSION['user_id'])) done(false, 'Please log in first', ['login' => true]);

$email = strtolower(trim($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) done(false, 'Invalid email');

q($conn, "CREATE TABLE IF NOT EXISTS subscribers (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(190) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$st = q($conn, "INSERT IGNORE INTO subscribers (email) VALUES (?)", $email);
if (mysqli_stmt_affected_rows($st) < 1) done(true, 'Already subscribed');
$sent = send_mail($email, 'Welcome to Deepkala Hair Salon', "Thank you for subscribing! We'll keep you posted on new styles, offers and memberships.\n\nDeepkala Hair Salon\n2 Jayvimal Society, Cadila Road, Ghodasar, Ahmedabad - 380050\n9:00 AM - 9:00 PM, Daily");
if (!$sent) { q($conn, "DELETE FROM subscribers WHERE email = ?", $email); done(false, 'Mail failed, try again'); }
done(true, 'Subscribed!');
