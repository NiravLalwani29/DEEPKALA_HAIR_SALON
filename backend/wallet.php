<?php
// Shared helpers: time slots, wallet, cancel + reschedule (used by admin and customer)
const DK_SLOTS = ['09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM','06:00 PM','07:00 PM','08:00 PM'];

function wallet_log($conn, $uid, $apptId, $type, $amt, $why) {
    $st = mysqli_prepare($conn, "INSERT INTO wallet_txn (user_id, appointment_id, type, amount, reason) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($st, 'iisis', $uid, $apptId, $type, $amt, $why);
    mysqli_stmt_execute($st);
    return mysqli_insert_id($conn);
}
function wallet_credit($conn, $uid, $amt, $apptId, $why) {
    if ($amt <= 0) return;
    $st = mysqli_prepare($conn, "UPDATE users SET wallet = wallet + ? WHERE id = ?");
    mysqli_stmt_bind_param($st, 'ii', $amt, $uid);
    mysqli_stmt_execute($st);
    wallet_log($conn, $uid, $apptId, 'credit', $amt, $why);
}
/* returns the new transaction id, or 0 if balance is not enough */
function wallet_debit($conn, $uid, $amt, $why) {
    $st = mysqli_prepare($conn, "UPDATE users SET wallet = wallet - ? WHERE id = ? AND wallet >= ?");
    mysqli_stmt_bind_param($st, 'iii', $amt, $uid, $amt);
    mysqli_stmt_execute($st);
    if (mysqli_stmt_affected_rows($st) < 1) return 0;
    return wallet_log($conn, $uid, null, 'debit', $amt, $why);
}

/* Cancel + refund the full amount to the customer's wallet. Returns null on success, or an error text. */
function cancel_appt($conn, $id, $by, $reason) {
    mysqli_begin_transaction($conn);
    try {
        $st = mysqli_prepare($conn, "SELECT user_id, amount, status, refunded FROM appointments WHERE id = ? FOR UPDATE");
        mysqli_stmt_bind_param($st, 'i', $id);
        mysqli_stmt_execute($st);
        $a = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        if (!$a) throw new RuntimeException('Appointment not found.');
        if ($a['status'] === 'cancelled') throw new RuntimeException('Already cancelled.');

        $refund = ((int) $a['refunded'] > 0) ? 0 : (int) $a['amount'];
        $st = mysqli_prepare($conn, "UPDATE appointments SET status='cancelled', cancelled_at=NOW(), admin_note=?, cancelled_by=?, refunded=? WHERE id=?");
        mysqli_stmt_bind_param($st, 'ssii', $reason, $by, $refund, $id);
        mysqli_stmt_execute($st);
        wallet_credit($conn, (int) $a['user_id'], $refund, $id, 'Refund: appointment #' . $id . ' cancelled ' . ($by === 'user' ? 'by you' : 'by the salon'));
        mysqli_commit($conn);
        return null;
    } catch (RuntimeException $e) {
        mysqli_rollback($conn); return $e->getMessage();
    } catch (Throwable $e) {
        mysqli_rollback($conn); error_log($e->getMessage()); return 'Something went wrong. Please try again.';
    }
}

/* Stylists (id => name) free at a slot: not booked there and not on leave that day. $skip = appointment id to ignore (reschedule). */
function free_barbers($conn, $date, $time, $skip = 0) {
    $st = mysqli_prepare($conn, "SELECT id, name FROM barbers
        WHERE id NOT IN (SELECT barber_id FROM appointments WHERE appointment_date=? AND appointment_time=? AND status<>'cancelled' AND id<>?)
        AND id NOT IN (SELECT barber_id FROM barber_leaves WHERE leave_date=?) ORDER BY id FOR UPDATE");
    mysqli_stmt_bind_param($st, 'ssis', $date, $time, $skip, $date);
    mysqli_stmt_execute($st);
    return array_column(mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC), 'name', 'id');
}

/* Move an appointment to a new date/time (barber auto-picked). Returns null on success, or an error text. */
function reschedule_appt($conn, $id, $by, $date, $time, $reason) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) return 'Choose a valid date.';
    if ($date < date('Y-m-d')) return 'The new date cannot be in the past.';
    if (!in_array($time, DK_SLOTS, true)) return 'Choose a valid time slot.';

    mysqli_begin_transaction($conn);
    try {
        $st = mysqli_prepare($conn, "SELECT appointment_date, appointment_time, barber_id, status FROM appointments WHERE id = ? FOR UPDATE");
        mysqli_stmt_bind_param($st, 'i', $id);
        mysqli_stmt_execute($st);
        $a = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        if (!$a) throw new RuntimeException('Appointment not found.');
        if ($a['status'] === 'cancelled') throw new RuntimeException('Cancelled appointments cannot be rescheduled.');
        if ($a['appointment_date'] === $date && $a['appointment_time'] === $time) throw new RuntimeException('That is the same date and time as before.');

        $free = array_keys(free_barbers($conn, $date, $time, $id));
        if (!$free) throw new RuntimeException('All stylists are booked or on leave then. Pick another slot or date.');
        $barber = in_array((int) $a['barber_id'], $free, true) ? (int) $a['barber_id'] : $free[0];

        $old = date('D, j M Y', strtotime($a['appointment_date'])) . ' at ' . $a['appointment_time'];
        $st = mysqli_prepare($conn, "UPDATE appointments SET appointment_date=?, appointment_time=?, barber_id=?, rescheduled_count=rescheduled_count+1, resched_note=?, resched_by=?, old_slot=? WHERE id=?");
        mysqli_stmt_bind_param($st, 'ssisssi', $date, $time, $barber, $reason, $by, $old, $id);
        mysqli_stmt_execute($st);
        mysqli_commit($conn);
        return null;
    } catch (RuntimeException $e) {
        mysqli_rollback($conn); return $e->getMessage();
    } catch (Throwable $e) {
        mysqli_rollback($conn); error_log($e->getMessage()); return 'Something went wrong. Please try again.';
    }
}
