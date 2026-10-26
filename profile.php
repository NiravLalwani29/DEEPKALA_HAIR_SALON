<?php
require 'backend/config.php';
date_default_timezone_set('Asia/Kolkata');

$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header("Location: login.php?redirect=profile.php");
    exit;
}
$uid = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') == 'update_profile') {
    header('Content-Type: application/json');
    $name  = trim(preg_replace('/\s+/', ' ', $_POST['name'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');

    if (!preg_match('/^[A-Za-z ]{2,60}$/', $name)) {
        echo json_encode(['ok' => false, 'error' => 'Name should be 2-60 characters, letters and spaces only.']);
        exit;
    }
    if (!preg_match('/^(\+91[\-\s]?)?[6-9]\d{9}$/', $phone)) {
        echo json_encode(['ok' => false, 'error' => 'Enter a valid 10-digit Indian mobile number.']);
        exit;
    }
    $n = mysqli_real_escape_string($conn, $name);
    $p = mysqli_real_escape_string($conn, $phone);
    mysqli_query($conn, "UPDATE users SET name = '$n', phone = '$p' WHERE id = '$uid'");
    $_SESSION['user_name']  = $name;
    $_SESSION['user_phone'] = $phone;
    echo json_encode(['ok' => true]);
    exit;
}

$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, phone, email, membership FROM users WHERE id = '$uid'"));
if (!$u) {
    session_unset();
    session_destroy();
    header("Location: login.php?redirect=profile.php");
    exit;
}
$_SESSION['membership'] = $u['membership'];

$appts = [];
$res = mysqli_query($conn, "SELECT a.*, b.name AS barber_name FROM appointments a
        LEFT JOIN barbers b ON a.barber_id = b.id
        WHERE a.user_id = '$uid' ORDER BY a.appointment_date DESC, a.id DESC");
while ($r = mysqli_fetch_assoc($res)) {
    $ts = strtotime($r['appointment_date'] . ' ' . $r['appointment_time']);
    if ($ts === false) { $ts = strtotime($r['appointment_date']); }
    $appts[] = [
        'service' => explode(' — ', $r['service'])[0],
        'date'    => $r['appointment_date'],
        'time'    => $r['appointment_time'],
        'ts'      => $ts * 1000,
        'barber'  => $r['barber_name'] ?? '—',
        'method'  => $r['payment_method'],
        'amount'  => (int) $r['amount'],
        'id'      => (int) $r['id'],
        'status'  => $r['status'] ?? 'pending',
        'note'    => $r['admin_note'] ?? '',
        'resched' => (int) ($r['rescheduled_count'] ?? 0),
        'cancelled_by' => $r['cancelled_by'] ?? '',
        'refunded'     => (int) ($r['refunded'] ?? 0),
        'resched_note' => $r['resched_note'] ?? '',
        'resched_by'   => $r['resched_by'] ?? '',
        'old_slot'     => $r['old_slot'] ?? '',
    ];
}
$walletBal = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT wallet FROM users WHERE id = $uid"))['wallet'] ?? 0);
$txns = [];
$tr = mysqli_query($conn, "SELECT w.*, a.service FROM wallet_txn w LEFT JOIN appointments a ON a.id = w.appointment_id
                           WHERE w.user_id = $uid ORDER BY w.id DESC LIMIT 100");
while ($t = mysqli_fetch_assoc($tr)) {
    $txns[] = [
        'type' => $t['type'], 'amount' => (int) $t['amount'], 'reason' => $t['reason'],
        'service' => $t['service'] ? explode(' — ', $t['service'])[0] : '',
        'appt' => $t['appointment_id'] ? (int) $t['appointment_id'] : 0,
        'ts' => strtotime($t['created_at']) * 1000,
    ];
}
$profileData = [
    'user'      => ['name' => $u['name'], 'email' => $u['email'], 'phone' => $u['phone']],
    'tier'      => $u['membership'],
    'appts'     => $appts,
    'wallet'    => $walletBal,
    'txns'      => $txns,
    'updated'   => isset($_GET['updated']),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile — Deepkala Hair Salon, Ahmedabad</title>

  <link rel="icon" type="image/png" href="assets/deepkala-emblem.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/theme.css?v=<?= filemtime(__DIR__ . '/assets/theme.css') ?>">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      background-color: var(--bg-cream);
      color: var(--text-ink);
      line-height: 1.6;
      overflow-x: hidden;
      width: 100%;
    }

    body { min-height: 100vh; display: flex; flex-direction: column; width: 100%; overflow-x: hidden; }

    img, svg { max-width: 100%; height: auto; }

    a { color: inherit; text-decoration: none; }

    .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 24px; }
@media (max-width: 768px) {
 .container { padding: 0 16px; }
}

    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; }

    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }

    .site-header { position: sticky; top: 0; z-index: 100; background-color: rgba(21, 19, 14, 0.95); backdrop-filter: blur(8px); border-bottom: 1px solid var(--border-hairline); transition: padding 0.3s ease, box-shadow 0.3s ease; }

    .header-inner { display: flex; align-items: center; justify-content: space-between; height: 76px; }

    .brand-logo { display: flex; align-items: center; gap: 12px; }

    .logo-text-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; font-size: 1.25rem; font-weight: 700; letter-spacing: 0.06em; color: var(--text-ink); line-height: 1.1; }

    .logo-text-sub { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; color: var(--bronze-taupe); }

    .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; }

    .nav-link { font-size: 0.825rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-ink); padding: 6px 0; position: relative; transition: color 0.2s ease; }

    .nav-link:hover, .nav-link.active { color: var(--rose-accent); }

    .nav-link.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background-color: var(--rose-accent); }

    .header-book-btn { display: inline-flex; align-items: center; justify-content: center; padding: 9px 20px; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; background-color: var(--rose-accent); color: var(--on-primary); border: 1px solid var(--rose-accent); border-radius: 4px; transition: all 0.2s ease; white-space: nowrap; }

    .header-book-btn:hover { background-color: var(--rose-accent-hover); border-color: var(--rose-accent-hover); color: var(--on-primary); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(10, 8, 6, 0.35); }

    .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; color: var(--text-ink); }

    .page-hero { position: relative; padding: 60px 0 40px 0; text-align: center; border-bottom: 1px solid var(--border-hairline); background-color: var(--bg-cream); overflow: hidden; isolation: isolate; }

    .page-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: var(--bg-cream);
      background-image:
        radial-gradient(circle at 25% 20%, rgba(201,166,107,0.12), transparent 55%),
        radial-gradient(circle at 80% 85%, rgba(201,166,107,0.07), transparent 50%),
        repeating-linear-gradient(135deg, rgba(255,255,255,0.04) 0px, rgba(255,255,255,0.04) 1px, transparent 1px, transparent 9px),
        repeating-linear-gradient(45deg, rgba(0,0,0,0.22) 0px, rgba(0,0,0,0.22) 1px, transparent 1px, transparent 9px),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/%3E%3C/svg%3E");
      background-size: auto, auto, auto, auto, 180px 180px;
      background-repeat: no-repeat, no-repeat, repeat, repeat, repeat;
      z-index: -2;
    }

    .page-hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(160deg, rgba(10,8,6,0.82) 0%, rgba(201,166,107,0.38) 55%, rgba(10,8,6,0.88) 100%); z-index: -1; }

    .page-hero > .container { position: relative; z-index: 1; }

    .page-hero h1 { font-size: clamp(2.2rem, 4vw, 3.2rem); font-weight: 700; color: var(--text-ink); margin-bottom: 12px; }

    .page-hero p { font-size: 1rem; color: var(--text-2); max-width: 620px; margin: 0 auto; }

    .eyebrow-tag { display: inline-block; background-color: var(--brand); color: var(--on-brand); padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; }

    .category-header { display: flex; align-items: center; gap: 16px; margin-top: 48px; margin-bottom: 16px; }

    .category-header .hairline { flex: 1; height: 1px; background-color: var(--border-hairline); }

    main { flex: 1 0 auto; }

    .whatsapp-float { position: fixed; bottom: max(20px, env(safe-area-inset-bottom, 20px)); right: max(20px, env(safe-area-inset-right, 20px)); width: 52px; height: 52px; background-color: var(--whatsapp-green); color: var(--on-whatsapp); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(37,211,102,0.35); z-index: 999; transition: transform 0.2s ease; }

    .whatsapp-float:hover { transform: scale(1.08) translateY(-2px); }

    .reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1); }

    .reveal.active { opacity: 1; transform: translateY(0); }

    .mobile-menu-cta { display: none; }
@media (max-width: 860px) {
      .nav-links { display: none; position: absolute; top: 100%; left: 0; right: 0; background-color: var(--bg-cream); border-bottom: 2px solid var(--rose-accent); flex-direction: column; padding: 16px 20px 24px 20px; gap: 8px; box-shadow: 0 12px 28px rgba(0,0,0,0.45); z-index: 999; max-height: calc(100vh - 80px); overflow-y: auto; }

      .nav-links.show { display: flex; }

      .nav-link { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; font-size: 0.9rem; width: 100%; border-bottom: 1px solid var(--border-hairline); border-radius: 4px; }

      .nav-link:hover, .nav-link.active { background-color: var(--card-cream); }

      .mobile-toggle { display: flex; align-items: center; justify-content: center; min-width: 44px; min-height: 44px; border: 1px solid var(--border-hairline); border-radius: 4px; }

      .mobile-menu-cta { display: block; margin-top: 8px; }
}

    .dk-member-badge { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; padding: 3px 9px; border-radius: 4px; white-space: nowrap; }

    .dk-badge-silver { background-color: rgba(201,166,107,0.15); color: #C9A66B; border: 1px solid rgba(201,166,107,0.4); }

    .dk-badge-gold { background-color: rgba(255,214,102,0.15); color: var(--highlight); border: 1px solid rgba(255,214,102,0.45); }

    .dk-nav-profile { display: none; }
@media (max-width: 860px) {
 .dk-nav-profile { display: block; }
}

    .nav-member-badge { margin-left: 8px; font-size: 0.55rem !important; padding: 2px 7px !important; vertical-align: middle; }
</style>
  <style>
    [hidden] { display: none !important; }

    .dk-price-old { text-decoration: line-through; color: var(--text-3); font-size: 0.78em; font-weight: 500; margin-right: 2px; }

    .dk-price-new { color: var(--rose-accent); font-weight: 700; }

    .pf-wrap { padding-bottom: 72px; }

    .pf-btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      padding: 10px 20px; min-height: 42px;
      font-family: inherit; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
      background-color: var(--rose-accent); color: var(--on-primary);
      border: 1px solid var(--rose-accent); border-radius: 4px;
      cursor: pointer; white-space: nowrap; transition: all 0.2s ease;
    }

    .pf-btn:hover { background-color: var(--rose-accent-hover); border-color: var(--rose-accent-hover); }

    .pf-btn-outline { background-color: transparent; color: var(--text-ink); border-color: var(--border-hairline); }

    .pf-btn-outline:hover { background-color: transparent; border-color: var(--rose-accent); color: var(--rose-accent); }

    .pf-btn-block { width: 100%; }

    .pf-notice { margin-top: 32px; padding: 14px 20px; background-color: var(--card-cream); border: 1px solid var(--rose-accent); color: #FFFFFF; font-size: 0.9rem; }

    .pf-summary { display: flex; align-items: center; gap: 28px; margin-top: 40px; padding: 32px; background-color: var(--card-white); border: 1px solid var(--border-hairline); }

    .pf-avatar {
      position: relative; flex: 0 0 auto; width: 88px; height: 88px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.9rem; font-weight: 700;
      color: var(--rose-accent); background-color: var(--card-cream); border: 1px solid var(--bronze-taupe);
    }

    .pf-avatar::after { content: ''; position: absolute; inset: 5px; border: 1px dashed var(--bronze-taupe); border-radius: 50%; opacity: 0.6; }

    .pf-identity { flex: 1 1 auto; min-width: 0; }

    .pf-name-row { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 10px; }

    .pf-name { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; line-height: 1.2; }

    .pf-contact { list-style: none; display: flex; flex-wrap: wrap; gap: 8px 24px; color: var(--text-2); font-size: 0.9rem; }

    .pf-contact li { display: flex; align-items: center; gap: 8px; min-width: 0; }

    .pf-contact svg { flex-shrink: 0; color: var(--bronze-taupe); }

    .pf-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 16px; }

    .pf-stat { padding: 20px; background-color: var(--card-white); border: 1px solid var(--border-hairline); }

    .pf-stat-value { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.6rem; font-weight: 700; line-height: 1.25; color: var(--text-ink); }

    .pf-stat-value.is-text { font-size: 1.15rem; padding-top: 5px; }

    .pf-stat-label { margin-top: 6px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--bronze-taupe); }

    .pf-grid { display: grid; grid-template-columns: minmax(0, 1fr); column-gap: 40px; }

    .pf-card { padding: 24px; background-color: var(--card-white); border: 1px solid var(--border-hairline); }

    .pf-card-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.2rem; font-weight: 700; margin-bottom: 8px; }

    .pf-card-text { font-size: 0.9rem; color: var(--text-2); margin-bottom: 20px; }

    .pf-empty { text-align: center; padding: 32px 24px; }

    .pf-empty p { color: var(--text-2); font-size: 0.92rem; margin-bottom: 18px; }

    .pf-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }

    .pf-appt {
      display: grid; grid-template-columns: 64px minmax(0, 1fr) auto; align-items: center; gap: 18px;
      padding: 18px 20px; background-color: var(--card-white); border: 1px solid var(--border-hairline);
    }

    .pf-appt.is-upcoming { border-color: var(--rose-accent); }

    .pf-appt-date { text-align: center; padding: 8px 0; background-color: var(--card-cream); border: 1px solid var(--border-hairline); }

    .pf-appt-day { display: block; font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.5rem; font-weight: 700; line-height: 1.1; color: var(--rose-accent); }

    .pf-appt-mon { display: block; margin-top: 2px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-2); }

    .pf-appt-service { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.1rem; font-weight: 700; line-height: 1.3; margin-bottom: 2px; }

    .pf-appt-meta { font-size: 0.85rem; color: var(--text-2); }

    .pf-appt-meta.is-faint { font-size: 0.75rem; color: var(--text-3); margin-top: 2px; }

    .pf-appt-trail { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; text-align: right; }

    .pf-appt-amount { font-size: 1rem; font-weight: 700; color: var(--text-ink); }

    .pf-appt-actions { grid-column: 1 / -1; padding-top: 14px; border-top: 1px solid rgba(122, 108, 85, 0.45); }

    .pf-more { display: flex; margin: 16px auto 0; }

    .pf-capped.is-open { max-height: 480px; overflow-y: auto; padding-right: 6px; scrollbar-width: thin; scrollbar-color: #A9884C transparent; }

    .pf-chip { display: inline-block; padding: 3px 9px; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; border-radius: 4px; white-space: nowrap; }

    .pf-chip-confirmed { background-color: rgba(201, 166, 107, 0.15); color: #C9A66B; border: 1px solid rgba(201, 166, 107, 0.4); }

    .pf-chip-done { background-color: rgba(255, 255, 255, 0.06); color: var(--text-3); border: 1px solid rgba(255, 255, 255, 0.18); }

    .pf-chip-pending { background-color: rgba(229, 160, 60, 0.14); color: var(--warning); border: 1px solid rgba(229, 160, 60, 0.45); }

    .pf-chip-cancelled { background-color: rgba(255, 107, 96, 0.12); color: #FF8A80; border: 1px solid rgba(255, 107, 96, 0.4); }

    .pf-appt-amount.is-void { text-decoration: line-through; color: var(--text-3); font-weight: 500; }

    .pf-member.is-gold { border-color: var(--gold-glow); box-shadow: 0 10px 30px rgba(255, 214, 102, 0.08); }

    .pf-member-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 4px; }

    .pf-member-discount { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.8rem; font-weight: 700; color: var(--rose-accent); }

    .pf-member.is-gold .pf-member-discount { color: var(--gold-glow); }

    .pf-kv { display: grid; gap: 14px; margin: 0 0 22px; }

    .pf-kv-row { display: flex; justify-content: space-between; gap: 16px; font-size: 0.9rem; }

    .pf-kv dt { color: var(--text-3); }

    .pf-kv dd { margin: 0; font-weight: 500; text-align: right; color: var(--text-ink); }

    .pf-links { list-style: none; }

    .pf-links li + li { border-top: 1px solid rgba(122, 108, 85, 0.45); }

    .pf-links a, .pf-links button {
      display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 14px 0;
      background: none; border: 0; color: var(--text-ink); font: inherit; font-size: 0.9rem; text-align: left; cursor: pointer; transition: color 0.2s ease;
    }

    .pf-links a:hover, .pf-links button:hover { color: var(--rose-accent); }

    .pf-links svg { color: var(--bronze-taupe); flex-shrink: 0; }

    .pf-overlay { display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 24px; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }

    .pf-overlay.show { display: flex; }

    .pf-modal { width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 32px; background-color: var(--bg-cream); border: 1px solid var(--border-hairline); border-radius: 8px; box-shadow: 0 20px 48px rgba(0, 0, 0, 0.5); }

    .pf-modal-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.4rem; font-weight: 700; margin-bottom: 6px; }

    .pf-modal-sub { font-size: 0.82rem; color: var(--text-2); margin-bottom: 22px; }

    .pf-form { display: grid; gap: 16px; }

    .pf-field { display: flex; flex-direction: column; gap: 6px; }

    .pf-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .pf-label { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: var(--text-ink); }

    .pf-input {
      width: 100%; min-height: 42px; padding: 11px 14px;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 15px; color: var(--text-ink);
      background-color: var(--card-white); border: 1px solid var(--border-hairline); border-radius: 4px; color-scheme: dark;
    }

    .pf-input:focus { outline: 2px solid var(--rose-accent); border-color: var(--rose-accent); }

    .pf-input.invalid { border-color: #FF6B60; }

    .pf-input[readonly] { opacity: 0.65; cursor: not-allowed; }

    textarea.pf-input { min-height: 88px; resize: vertical; }

    .pf-hint { font-size: 0.72rem; color: var(--text-3); }

    .pf-error { display: none; font-size: 0.72rem; color: #FF6B60; }

    .pf-error.show { display: block; }

    .pf-alert { display: none; padding: 9px 13px; font-size: 0.78rem; color: #FF8A80; background: rgba(255, 107, 96, 0.12); border: 1px solid rgba(255, 107, 96, 0.4); border-radius: 8px; }

    .pf-alert.show { display: block; }

    .pf-modal-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 12px; margin-top: 8px; }

    body.pf-modal-lock { overflow: hidden; }
@media (min-width: 720px) {
      .pf-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}
@media (min-width: 980px) {
      .pf-grid { grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr); align-items: start; }
}
@media (max-width: 640px) {
      .pf-summary { flex-direction: column; align-items: flex-start; padding: 24px 20px; }

      .pf-summary .pf-btn { width: 100%; }

      .pf-modal { padding: 24px 20px; }
}
@media (max-width: 560px) {
      .pf-appt { grid-template-columns: 52px minmax(0, 1fr); gap: 14px; padding: 16px; }

      .pf-appt-trail { grid-column: 1 / -1; flex-direction: row; align-items: center; justify-content: space-between; }

      .pf-field-row { grid-template-columns: 1fr; }
}
</style>
  <style>
    @view-transition {
      navigation: auto;
    }
</style>
  <style>
    .skip-link {
      position: absolute;
      top: -48px;
      left: 8px;
      z-index: 3000;
      background-color: var(--rose-accent, #C9A66B);
      color: var(--on-primary);
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-weight: 700;
      font-size: 0.85rem;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      transition: top 0.2s ease;
    }

    .skip-link:focus {
      top: 8px;
    }

    a:focus-visible,
    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    [tabindex]:focus-visible,
    summary:focus-visible {
      outline: 2px solid var(--rose-accent, #C9A66B);
      outline-offset: 2px;
      border-radius: 4px;
    }
@media (prefers-reduced-motion: reduce) {
      .reveal {
        opacity: 1;
        transform: none;
        transition: none;
      }
}
</style>
  <noscript>
    <style>
      .reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
</style>
  </noscript>
  <link rel="stylesheet" href="assets/responsive.css?v=<?= filemtime(__DIR__ . '/assets/responsive.css') ?>">
</head>
<body>
  <a href="#main-content" class="skip-link">Skip to content</a>
  <header class="site-header" id="siteHeader">
    <div class="container header-inner">
      <a href="index.php" class="brand-logo" aria-label="Deepkala Hair Salon Homepage">
        <img src="assets/deepkala-emblem.png" width="38" height="38" alt="Deepkala Hair Salon" style="border-radius:6px;object-fit:cover;">
        <div>
          <div class="logo-text-title">DEEPKALA</div>
          <div class="logo-text-sub">HAIR SALON</div>
        </div>
      </a>

      <nav aria-label="Main Navigation">
        <ul class="nav-links" id="navLinks">
          <li><a href="index.php" class="nav-link">Home</a></li>
          <li><a href="services.php" class="nav-link">Services</a></li>
          <li><a href="reviews.php" class="nav-link">Reviews</a></li>
          <li><a href="about.php" class="nav-link">About</a></li>
          <li><a href="contact.php" class="nav-link">Contact</a></li>
          <li><a href="membership.php" class="nav-link">Membership<span id="navMemberBadge"><?php $dkTier = $_SESSION['membership'] ?? 'none'; if ($dkTier === 'gold' || $dkTier === 'silver') { echo '<span class="dk-member-badge dk-badge-' . $dkTier . ' nav-member-badge">' . ucfirst($dkTier) . '</span>'; } ?></span></a></li>
          <li class="dk-nav-profile"><a href="profile.php" class="nav-link active" aria-current="page"><span class="nav-underline" aria-hidden="true"></span>My Profile</a></li>
          <li class="mobile-menu-cta">
            <a href="https://wa.me/919924620958?text=Hello%20Deepkala%20Hair%20Salon" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-cta">Chat on WhatsApp</a>
          </li>
        </ul>
      </nav>

      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="booking.php" class="header-book-btn" title="Book Appointment at Deepkala Hair Salon">Book Appointment</a>
        <?php include 'backend/profile_menu.php'; ?>
        <button class="mobile-toggle" id="mobileMenuBtn" aria-controls="navLinks" aria-label="Toggle navigation menu">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
      </div>
    </div>
  </header>
  <main id="main-content" tabindex="-1">
    <section class="page-hero">
      <div class="container reveal">
        <h1 class="font-serif">My Profile</h1>
        <p>Your details, membership and visit history at Deepkala Hair Salon.</p>
      </div>
    </section>

    <div class="container pf-wrap">

      <div class="pf-notice" id="pfNotice" role="status" aria-live="polite" hidden></div>

      <div id="pfApp" hidden>

        <section class="pf-summary reveal" aria-label="Profile summary">
          <div class="pf-avatar" id="pfAvatar" aria-hidden="true"></div>
          <div class="pf-identity">
            <div class="pf-name-row">
              <h2 class="font-serif pf-name" id="pfName"></h2>
              <span id="pfTierBadge"></span>
            </div>
            <ul class="pf-contact">
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/></svg>
                <span id="pfEmail"></span>
              </li>
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span id="pfPhone"></span>
              </li>
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Client since <span id="pfSince"></span></span>
              </li>
            </ul>
          </div>
          <button type="button" class="pf-btn pf-btn-outline" data-action="edit">Edit Profile</button>
        </section>

        <div class="pf-stats reveal" id="pfStats"></div>

        <div class="pf-grid">

          <div class="pf-col">
            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Upcoming Appointment</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfUpcoming"></div>

            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Visit History</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfHistory"></div>
          </div>

          <div class="pf-col">
            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Wallet</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfWallet"></div>

            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Membership</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfMembership"></div>

            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Account</h2>
              <div class="hairline"></div>
            </div>
            <div class="pf-card">
              <ul class="pf-links">
                <li><a href="booking.php">Book an Appointment <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg></a></li>
                <li><a href="membership.php">Membership Plans <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg></a></li>
                <li><a href="contact.php">Contact the Salon <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg></a></li>
                <li><button type="button" data-action="logout">Logout <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button></li>
              </ul>
            </div>
          </div>

        </div>
      </div>

    </div>
  </main>

  <div class="pf-overlay" id="pfEditOverlay">
    <div class="pf-modal" role="dialog" aria-modal="true" aria-labelledby="pfEditTitle">
      <h2 class="pf-modal-title" id="pfEditTitle">Edit Profile</h2>
      <p class="pf-modal-sub">Update your name and phone number.</p>

      <form class="pf-form" id="pfEditForm" novalidate>
        <div class="pf-alert" id="pfEditAlert" role="alert"></div>

        <div class="pf-field">
          <label class="pf-label" for="pfFieldName">Full name</label>
          <input type="text" id="pfFieldName" class="pf-input" autocomplete="name" maxlength="60" aria-describedby="err-pfFieldName">
          <span class="pf-error" id="err-pfFieldName">Enter your full name (2&ndash;60 characters).</span>
        </div>

        <div class="pf-field-row">
          <div class="pf-field">
            <label class="pf-label" for="pfFieldEmail">Email</label>
            <input type="email" id="pfFieldEmail" class="pf-input" readonly aria-describedby="hint-pfFieldEmail">
            <span class="pf-hint" id="hint-pfFieldEmail">Email is your login and can&rsquo;t be changed.</span>
          </div>
          <div class="pf-field">
            <label class="pf-label" for="pfFieldPhone">Phone number</label>
            <input type="tel" id="pfFieldPhone" class="pf-input" autocomplete="tel" aria-describedby="err-pfFieldPhone">
            <span class="pf-error" id="err-pfFieldPhone">Enter a valid phone number.</span>
          </div>
        </div>

        <div class="pf-modal-actions">
          <button type="button" class="pf-btn pf-btn-outline" id="pfEditCancel">Cancel</button>
          <button type="submit" class="pf-btn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

<?php include 'backend/footer.php'; ?>

  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const observerOptions = { threshold: 0.1 };
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('active');
          }
        });
      }, observerOptions);

      document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

      const menuBtn = document.getElementById('mobileMenuBtn');
      const navLinks = document.getElementById('navLinks');
      if (menuBtn && navLinks) {
        menuBtn.setAttribute('aria-expanded', 'false');

        const toggleMenu = (show) => {
          const isOpen = show !== undefined ? show : !navLinks.classList.contains('show');
          navLinks.classList.toggle('show', isOpen);
          menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        menuBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          toggleMenu();
        });

        document.addEventListener('click', (e) => {
          if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) {
            toggleMenu(false);
          }
        });

        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            toggleMenu(false);
          }
        });

        navLinks.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', () => toggleMenu(false));
        });
      }

      const header = document.getElementById('siteHeader');
      if (header) {
        window.addEventListener('scroll', () => {
          if (window.scrollY > 20) {
            header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.35)';
          } else {
            header.style.boxShadow = 'none';
          }
        }, { passive: true });
      }
    });
  </script>

  <script>
    const DK_USER_KEY = 'dk_user';
    const DK_MEMBERSHIP_KEY = 'dk_membership';

    const DK_SESSION_USER = <?php echo json_encode(isset($_SESSION['user_id']) ? ['name' => $_SESSION['user_name'], 'email' => $_SESSION['user_email'], 'phone' => $_SESSION['user_phone']] : null); ?>;
    const DK_SESSION_TIER = <?php echo json_encode($_SESSION['membership'] ?? 'none'); ?>;
    function dkGetUser() { return DK_SESSION_USER; }
    function dkGetMembership() {
      if (DK_SESSION_TIER === 'gold') return { tier: 'gold', discount: 20 };
      if (DK_SESSION_TIER === 'silver') return { tier: 'silver', discount: 10 };
      return null;
    }
    function dkLogout() { window.location.href = 'logout.php'; }
    function dkTierLabel(tier) {
      return tier === 'gold' ? 'Gold Member' : tier === 'silver' ? 'Silver Member' : '';
    }

    function dkApplyMemberPricing() {
      const membership = dkGetMembership();
      const priceEls = document.querySelectorAll('.service-price');
      priceEls.forEach(el => {
        if (!el.dataset.dkOriginal) {
          el.dataset.dkOriginal = el.textContent.trim();
        }
        const original = el.dataset.dkOriginal;
        const num = parseInt(original.replace(/[^\d]/g, ''), 10);
        if (isNaN(num)) return;
        if (membership && membership.discount) {
          const discounted = Math.round(num * (1 - membership.discount / 100));
          el.innerHTML = '<span class="dk-price-old">₹' + num.toLocaleString('en-IN') + '</span><span class="dk-price-new">₹' + discounted.toLocaleString('en-IN') + '</span>';
          el.classList.add('dk-member-priced');
        } else {
          el.textContent = original;
          el.classList.remove('dk-member-priced');
        }
      });
    }

    function dkRenderAuthNav() {}

    document.addEventListener('DOMContentLoaded', () => {
      dkApplyMemberPricing();
      dkRenderAuthNav();
    });
  </script>

  <script>
    (function () {
      'use strict';
      const D = <?php echo json_encode($profileData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      const PREVIEW = 5;
      const WHATSAPP_NUMBER = '919924620958';
      const showAll = {};
      let lastFocus = null;
      let noticeTimer = null;

      const $ = (id) => document.getElementById(id);
      const esc = (v) => String(v == null ? '' : v).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
      const money = (n) => '₹' + Number(n || 0).toLocaleString('en-IN');
      const fmtDate = (d) => d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
      const fmtLongDate = (d) => d.toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
      const fmtMonthYear = (d) => d.toLocaleDateString('en-IN', { month: 'long', year: 'numeric' });
      const fmtMonthShort = (d) => d.toLocaleDateString('en-IN', { month: 'short' });

      function relativeDays(d) {
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const day = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        const diff = Math.round((today - day) / 86400000);
        if (diff <= 0) return 'Today';
        if (diff === 1) return 'Yesterday';
        if (diff < 60) return diff + ' days ago';
        return Math.round(diff / 30) + ' months ago';
      }
      function initials(name) {
        const w = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (!w.length) return '?';
        return (w[0].charAt(0) + (w.length > 1 ? w[w.length - 1].charAt(0) : '')).toUpperCase();
      }

      function statusInfo(a) {
        if (a.status === 'cancelled') return { cls: 'pf-chip-cancelled', label: 'Cancelled' };
        if (a.status === 'approved') return a.ts >= Date.now() ? { cls: 'pf-chip-confirmed', label: 'Confirmed' } : { cls: 'pf-chip-done', label: 'Completed' };
        return { cls: 'pf-chip-pending', label: a.ts >= Date.now() ? 'Pending approval' : 'Pending' };
      }

      function renderAppointment(a, upcoming) {
        const when = upcoming ? fmtLongDate(a.start) : fmtDate(a.start);
        const waText = 'Hello Deepkala Hair Salon, I would like to reschedule or cancel my ' + a.service + ' appointment on ' + fmtDate(a.start) + ' at ' + a.time + '.';
        const cancelled = a.status === 'cancelled';
        const st = statusInfo(a);
        const actions = (upcoming && !cancelled)
          ? '<div class="pf-appt-actions" style="display:flex;gap:10px;flex-wrap:wrap"><button type="button" class="pf-btn pf-btn-outline" data-action="u-resched" data-id="' + a.id + '">Reschedule</button><button type="button" class="pf-btn pf-btn-outline" data-action="u-cancel" data-id="' + a.id + '">Cancel &amp; refund to wallet</button></div>'
          : '';
        const by = (w) => w === 'user' ? 'by you' : 'by the salon';
        const extra =
          (cancelled ? '<p class="pf-appt-meta is-faint">Cancelled ' + by(a.cancelled_by) + (a.note ? ' &mdash; Reason: ' + esc(a.note) : '') + '</p>' + (a.refunded > 0 ? '<p class="pf-appt-meta is-faint">' + money(a.refunded) + ' refunded to your wallet</p>' : '') : '') +
          (!cancelled && a.resched > 0 ? '<p class="pf-appt-meta is-faint">Rescheduled ' + by(a.resched_by) + (a.resched_note ? ' &mdash; Reason: ' + esc(a.resched_note) : '') + (a.old_slot ? ' (was ' + esc(a.old_slot) + ')' : '') + '</p>' : '');
        return '<li class="pf-appt' + (upcoming ? ' is-upcoming' : '') + '">' +
          '<div class="pf-appt-date" aria-hidden="true"><span class="pf-appt-day">' + a.start.getDate() + '</span><span class="pf-appt-mon">' + esc(fmtMonthShort(a.start)) + '</span></div>' +
          '<div>' +
            '<h3 class="pf-appt-service">' + esc(a.service) + '</h3>' +
            '<p class="pf-appt-meta">' + esc(when) + ' &middot; ' + esc(a.time) + '</p>' +
            '<p class="pf-appt-meta">with ' + esc(a.barber) + '</p>' +
            '<p class="pf-appt-meta is-faint">Paid via ' + esc(a.method) + '</p>' +
            extra +
          '</div>' +
          '<div class="pf-appt-trail">' +
            '<span class="pf-chip ' + st.cls + '">' + st.label + '</span>' +
            '<span class="pf-appt-amount' + (cancelled ? ' is-void' : '') + '">' + money(a.amount) + '</span>' +
          '</div>' +
          actions +
        '</li>';
      }

      function renderUpcoming(list) {
        if (!list.length) return '<div class="pf-card pf-empty"><p>You have no upcoming appointments.</p><a href="booking.php" class="pf-btn">Book Appointment</a></div>';
        return '<ul class="pf-list">' + list.map((a) => renderAppointment(a, true)).join('') + '</ul>';
      }
      function renderHistory(list) {
        if (!list.length) return '<div class="pf-card pf-empty"><p>Your completed visits will appear here.</p></div>';
        return capped('history', list.map((a) => renderAppointment(a, false)), '');
      }
      // First PREVIEW rows, then "Load more" opens the rest inside a fixed-height scroll box instead of stretching the page
      function capped(key, rows, style) {
        const open = showAll[key];
        let html = '<div class="pf-capped' + (open ? ' is-open' : '') + '"><ul class="pf-list"' + style + '>' + (open ? rows : rows.slice(0, PREVIEW)).join('') + '</ul></div>';
        if (rows.length > PREVIEW) {
          html += '<button type="button" class="pf-btn pf-btn-outline pf-more" data-action="toggle-' + key + '" aria-expanded="' + !!open + '">' +
            (open ? 'Show fewer' : 'Load more (' + (rows.length - PREVIEW) + ' more)') + '</button>';
        }
        return html;
      }
      function renderMembership(tier) {
        if (tier !== 'gold' && tier !== 'silver') {
          return '<div class="pf-card"><p class="pf-card-title">Not a member yet</p>' +
            '<p class="pf-card-text">Save on every service with a Silver or Gold membership. Your discount is applied automatically at booking.</p>' +
            '<a href="membership.php" class="pf-btn pf-btn-block">View Membership Plans</a></div>';
        }
        const discount = tier === 'gold' ? 20 : 10;
        const label = tier === 'gold' ? 'Gold Member' : 'Silver Member';
        return '<div class="pf-card pf-member' + (tier === 'gold' ? ' is-gold' : '') + '">' +
          '<div class="pf-member-head"><span class="dk-member-badge dk-badge-' + tier + '">' + label + '</span><span class="pf-member-discount">' + discount + '% off</span></div>' +
          '<dl class="pf-kv"><div class="pf-kv-row"><dt>Plan</dt><dd>' + (tier === 'gold' ? 'Gold' : 'Silver') + '</dd></div>' +
          '<div class="pf-kv-row"><dt>Discount</dt><dd>' + discount + '% on all services</dd></div></dl>' +
          '<a href="membership.php" class="pf-btn pf-btn-outline pf-btn-block">Manage Membership</a></div>';
      }

      function renderWallet() {
        const tx = D.txns || [];
        let h = '<div class="pf-card"><p class="pf-card-title">Balance</p><p class="pf-stat-value" style="margin:6px 0 4px">' + money(D.wallet || 0) + '</p></div>';
        if (tx.length) {
          h += capped('wallet', tx.map((t) => {
            const cr = t.type === 'credit';
            return '<li class="pf-appt" style="grid-template-columns:1fr auto"><div>' +
              '<h3 class="pf-appt-service">' + (cr ? 'Refund received' : 'Used for booking') + '</h3>' +
              '<p class="pf-appt-meta">' + esc(fmtDate(new Date(t.ts))) + (t.service ? ' &middot; ' + esc(t.service) : '') + (t.appt ? ' &middot; Appt #' + t.appt : '') + '</p>' +
              '<p class="pf-appt-meta is-faint">' + esc(t.reason || '') + '</p></div>' +
              '<span class="pf-appt-amount" style="color:' + (cr ? '#6EB478' : '#E0665A') + '">' + (cr ? '+' : '&minus;') + money(t.amount) + '</span></li>';
          }), ' style="margin-top:12px"');
        } else {
          h += '<div class="pf-card pf-empty" style="margin-top:12px"><p>No wallet activity yet.</p></div>';
        }
        return h;
      }

      function askAction(kind, id) {
        const rs = kind === 'u-resched';
        const slots = ['09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM','06:00 PM','07:00 PM','08:00 PM'];
        const today = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 10);
        const ov = document.createElement('div');
        ov.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.72);display:flex;align-items:center;justify-content:center;z-index:9999;padding:20px';
        const f = 'width:100%;padding:10px;border-radius:6px;border:1px solid var(--border-strong);background:var(--surface-2);color:var(--text);font:inherit;margin:6px 0 12px';
        ov.innerHTML = '<div style="background:var(--surface);border:1px solid var(--border-strong);border-radius:10px;padding:24px;max-width:380px;width:100%;color:var(--text)">' +
          '<h3 style="margin-bottom:14px">' + (rs ? 'Reschedule appointment' : 'Cancel appointment') + '</h3>' +
          (rs ? '<label style="font-size:.8rem">New date</label><input type="date" id="uaDate" min="' + today + '" style="' + f + '"><label style="font-size:.8rem">New time</label><select id="uaTime" style="' + f + '">' + slots.map((s) => '<option>' + s + '</option>').join('') + '</select>'
              : '<p style="font-size:.85rem;margin-bottom:12px;color:#A9884C">The full amount you paid will be added to your wallet.</p>') +
          '<label style="font-size:.8rem">Reason (optional)</label><textarea id="uaReason" rows="2" maxlength="250" style="' + f + '"></textarea>' +
          '<p id="uaErr" style="color:#E0665A;font-size:.8rem;margin-bottom:10px"></p>' +
          '<div style="display:flex;gap:10px"><button type="button" class="pf-btn pf-btn-outline" id="uaBack" style="flex:1">Back</button><button type="button" class="pf-btn" id="uaGo" style="flex:1">' + (rs ? 'Reschedule' : 'Cancel & refund') + '</button></div></div>';
        document.body.appendChild(ov);
        document.getElementById('uaBack').onclick = () => ov.remove();
        document.getElementById('uaGo').onclick = () => {
          const body = new FormData();
          body.append('action', rs ? 'reschedule' : 'cancel');
          body.append('id', id);
          body.append('reason', document.getElementById('uaReason').value);
          if (rs) { body.append('date', document.getElementById('uaDate').value); body.append('time', document.getElementById('uaTime').value); }
          fetch('user_action.php', { method: 'POST', body: body }).then((r) => r.json()).then((res) => {
            if (!res.ok) { document.getElementById('uaErr').textContent = res.error || 'Could not complete this. Try again.'; return; }
            alert(res.message); location.reload();
          }).catch(() => { document.getElementById('uaErr').textContent = 'Network error. Try again.'; });
        };
      }

      function render() {
        const user = D.user;
        const now = Date.now();
        const appts = D.appts.map((a) => Object.assign({}, a, { start: new Date(a.ts) })).filter((a) => !isNaN(a.start));
        // Upcoming = still on the calendar. History = already happened, or cancelled by the salon.
        const upcoming = appts.filter((a) => a.status !== 'cancelled' && a.ts >= now).sort((x, y) => x.ts - y.ts);
        const past = appts.filter((a) => a.status === 'cancelled' || a.ts < now).sort((x, y) => y.ts - x.ts);

        // Only appointments the salon has APPROVED count towards what the customer has spent.
        // (First visit Rs 350 approved -> 350; second visit Rs 450 approved -> 800.)
        const approved = appts.filter((a) => a.status === 'approved');
        const completed = approved.filter((a) => a.ts < now).sort((x, y) => y.ts - x.ts);

        const spent = approved.reduce((s, a) => s + a.amount, 0);
        const counts = {};
        approved.forEach((a) => { counts[a.service] = (counts[a.service] || 0) + 1; });
        let favourite = '';
        approved.forEach((a) => { if (!favourite || counts[a.service] > counts[favourite]) favourite = a.service; });
        const earliest = appts.length ? appts.reduce((m, a) => (a.start < m ? a.start : m), appts[0].start) : null;

        $('pfApp').hidden = false;
        $('pfAvatar').textContent = initials(user.name);
        $('pfName').textContent = user.name || 'Guest';
        $('pfEmail').textContent = user.email;
        $('pfPhone').textContent = user.phone || 'No phone added';
        $('pfSince').textContent = earliest ? fmtMonthYear(earliest) : '—';
        $('pfTierBadge').innerHTML = (D.tier === 'gold' || D.tier === 'silver')
          ? '<span class="dk-member-badge dk-badge-' + D.tier + '">' + (D.tier === 'gold' ? 'Gold Member' : 'Silver Member') + '</span>' : '';

        const stats = [
          { label: 'Completed visits', value: String(completed.length) },
          { label: 'Total spent', value: money(spent) },
          { label: 'Last visit', value: completed.length ? relativeDays(completed[0].start) : '—', text: true },
          { label: 'Most booked', value: favourite || '—', text: true }
        ];
        $('pfStats').innerHTML = stats.map((s) =>
          '<div class="pf-stat"><div class="pf-stat-value' + (s.text ? ' is-text' : '') + '">' + esc(s.value) + '</div><div class="pf-stat-label">' + esc(s.label) + '</div></div>'
        ).join('');

        $('pfUpcoming').innerHTML = '<span id="upcoming"></span>' + renderUpcoming(upcoming);
        $('pfHistory').innerHTML = '<span id="past"></span>' + renderHistory(past);
        $('pfMembership').innerHTML = renderMembership(D.tier);
        $('pfWallet').innerHTML = renderWallet();
      }

      function showNotice(message) {
        const el = $('pfNotice');
        el.textContent = message;
        el.hidden = false;
        clearTimeout(noticeTimer);
        noticeTimer = setTimeout(() => { el.hidden = true; }, 5000);
      }

      function fieldError(id, show) {
        const err = $('err-' + id), input = $(id);
        if (err) err.classList.toggle('show', show);
        if (input) { input.classList.toggle('invalid', show); input.setAttribute('aria-invalid', show ? 'true' : 'false'); }
      }
      function showAlert(m) { const el = $('pfEditAlert'); el.textContent = m; el.classList.add('show'); }
      function hideAlert() { $('pfEditAlert').classList.remove('show'); }

      function openEdit() {
        $('pfFieldName').value = D.user.name || '';
        $('pfFieldEmail').value = D.user.email || '';
        $('pfFieldPhone').value = D.user.phone || '';
        ['pfFieldName', 'pfFieldPhone'].forEach((id) => fieldError(id, false));
        hideAlert();
        lastFocus = document.activeElement;
        $('pfEditOverlay').classList.add('show');
        document.body.classList.add('pf-modal-lock');
        $('pfFieldName').focus();
      }
      function closeEdit() {
        $('pfEditOverlay').classList.remove('show');
        document.body.classList.remove('pf-modal-lock');
        if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        lastFocus = null;
      }

      function saveEdit(e) {
        e.preventDefault();
        hideAlert();
        const name = $('pfFieldName').value.trim().replace(/\s+/g, ' ');
        const phone = $('pfFieldPhone').value.trim();
        const nameOk = /^[A-Za-z ]{2,60}$/.test(name);
        const phoneOk = /^(\+91[\-\s]?)?[6-9]\d{9}$/.test(phone);
        fieldError('pfFieldName', !nameOk);
        fieldError('pfFieldPhone', !phoneOk);
        if (!nameOk || !phoneOk) return;

        const body = new FormData();
        body.append('action', 'update_profile');
        body.append('name', name);
        body.append('phone', phone);
        fetch('profile.php', { method: 'POST', body: body })
          .then((r) => r.json())
          .then((res) => {
            if (!res.ok) { showAlert(res.error || 'We could not save your changes. Please try again.'); return; }
            window.location.href = 'profile.php?updated=1'; // reload so the navbar menu shows the new name too
          })
          .catch(() => showAlert('We could not save your changes. Please try again.'));
      }

      function onKeydown(e) {
        const overlay = $('pfEditOverlay');
        if (!overlay.classList.contains('show')) return;
        if (e.key === 'Escape') { closeEdit(); return; }
        if (e.key !== 'Tab') return;
        const f = overlay.querySelectorAll('button:not([disabled]), input:not([disabled]), textarea:not([disabled])');
        if (!f.length) return;
        const first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }

      function onAppClick(e) {
        const el = e.target.closest('[data-action]');
        if (!el) return;
        const action = el.dataset.action;
        if (action === 'edit') openEdit();
        else if (action.startsWith('toggle-')) {
          showAll[action.slice(7)] = !showAll[action.slice(7)];
          render();
          const btn = document.querySelector('[data-action="' + action + '"]');
          if (btn) btn.focus();
        } else if (action === 'u-cancel' || action === 'u-resched') {
          askAction(action, el.dataset.id);
        } else if (action === 'logout') window.location.href = 'logout.php';
      }

      function init() {
        $('pfApp').addEventListener('click', onAppClick);
        $('pfEditForm').addEventListener('submit', saveEdit);
        $('pfEditCancel').addEventListener('click', closeEdit);
        $('pfEditOverlay').addEventListener('click', (e) => { if (e.target === e.currentTarget) closeEdit(); });
        document.addEventListener('keydown', onKeydown);
        render();
        if (D.updated) showNotice('Your profile has been updated.');
        if (location.hash) { const t = document.querySelector(location.hash); if (t) t.scrollIntoView(); }
      }
      document.addEventListener('DOMContentLoaded', init);
    })();
  </script>

</body>
</html>
