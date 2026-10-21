<?php
// Customer cancels / reschedules their own upcoming appointment (called from profile.php)
require 'backend/config.php';
require 'backend/wallet.php';
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');

function out($ok, $msg) { echo json_encode(['ok' => $ok, ($ok ? 'message' : 'error') => $msg]); exit; }

if (empty($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'Please login again.');
$uid = (int) $_SESSION['user_id'];
$id  = (int) ($_POST['id'] ?? 0);
$act = $_POST['action'] ?? '';
$reason = trim(mb_substr($_POST['reason'] ?? '', 0, 250));

$st = mysqli_prepare($conn, "SELECT user_id, amount, status, appointment_date, appointment_time FROM appointments WHERE id = ?");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$a = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

if (!$a || (int) $a['user_id'] !== $uid) out(false, 'Appointment not found.');
if ($a['status'] === 'cancelled') out(false, 'This appointment is already cancelled.');
if (strtotime($a['appointment_date'] . ' ' . $a['appointment_time']) < time()) out(false, 'Past appointments cannot be changed.');

if ($act === 'cancel') {
    $err = cancel_appt($conn, $id, 'user', $reason !== '' ? $reason : 'Cancelled by customer');
    $err ? out(false, $err) : out(true, 'Appointment cancelled. Rs. ' . (int) $a['amount'] . ' has been added to your wallet.');
}
if ($act === 'reschedule') {
    $err = reschedule_appt($conn, $id, 'user', $_POST['date'] ?? '', $_POST['time'] ?? '', $reason !== '' ? $reason : 'Rescheduled by customer');
    $err ? out(false, $err) : out(true, 'Appointment rescheduled.');
}
out(false, 'Unknown action.');
