<?php
require '../backend/config.php';
require '../backend/wallet.php';
date_default_timezone_set('Asia/Kolkata');
if (empty($_SESSION['admin_id'])) { header('Location: ../login.php?redirect=admin/index.php'); exit; }
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

/* ---------- Actions: approve / reschedule / cancel ---------- */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $id     = (int) ($_POST['id'] ?? 0);
    $act    = $_POST['action'] ?? '';
    $reason = trim(mb_substr($_POST['reason'] ?? '', 0, 250));
    $err = null; $ok = '';

    if ($act === 'approve') {
        mysqli_query($conn, "UPDATE appointments SET status='approved', approved_at=NOW() WHERE id=$id AND status='pending'");
        $ok = 'Appointment #' . $id . ' approved.';
    } elseif ($act === 'leave_add' || $act === 'leave_del') {
        $bid = (int) ($_POST['barber'] ?? 0); $ld = $_POST['date'] ?? '';
        $dd = DateTime::createFromFormat('Y-m-d', $ld);
        if (!$dd || $dd->format('Y-m-d') !== $ld || !$bid) { $err = 'Choose a stylist and a valid date.'; }
        else {
            $st = mysqli_prepare($conn, $act === 'leave_add' ? "INSERT IGNORE INTO barber_leaves (barber_id, leave_date) VALUES (?, ?)" : "DELETE FROM barber_leaves WHERE barber_id=? AND leave_date=?");
            mysqli_stmt_bind_param($st, 'is', $bid, $ld); mysqli_stmt_execute($st);
            $n = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE barber_id=$bid AND appointment_date='$ld' AND status<>'cancelled'"))[0];
            $ok = $act === 'leave_add' ? 'Leave saved.' . ($n ? " $n existing booking(s) that day still need rescheduling." : '') : 'Leave removed.';
        }
    } elseif ($reason === '') {
        $err = 'Please write a reason — the customer will see it.';
    } elseif ($act === 'cancel') {
        $err = cancel_appt($conn, $id, 'admin', $reason);
        $ok = 'Appointment #' . $id . ' cancelled. Amount refunded to the customer\'s wallet.';
    } elseif ($act === 'reschedule') {
        $err = reschedule_appt($conn, $id, 'admin', $_POST['date'] ?? '', $_POST['time'] ?? '', $reason);
        $ok = 'Appointment #' . $id . ' rescheduled.';
    }
    $_SESSION['flash'] = $err ? ['bad', $err] : ['good', $ok];
    header('Location: index.php#appts'); exit;
}
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);

/* ---------- Dashboard numbers ---------- */
$today = date('Y-m-d');
$m1 = date('Y-m-01');
$m0 = date('Y-m-01', strtotime('first day of last month'));
$cut = date('Y-m-d H:i:s', time() - 3600); // approved slots that ended before this are completed

$k = mysqli_fetch_assoc(mysqli_query($conn, "SELECT
  COALESCE(SUM(status='pending'),0) p, COALESCE(SUM(status='approved'),0) a, COALESCE(SUM(status='cancelled'),0) c, COUNT(*) t,
  COALESCE(SUM(CASE WHEN status='approved' THEN amount END),0) rev,
  COALESCE(SUM(CASE WHEN status='pending' THEN amount END),0) pend_val,
  COALESCE(SUM(CASE WHEN status='approved' AND appointment_date>='$m1' THEN amount END),0) rev_m,
  COALESCE(SUM(CASE WHEN status='approved' AND appointment_date>='$m0' AND appointment_date<'$m1' THEN amount END),0) rev_l,
  COALESCE(SUM(status='approved' AND STR_TO_DATE(CONCAT(appointment_date,' ',appointment_time),'%Y-%m-%d %h:%i %p') <= '$cut'),0) done,
  COALESCE(SUM(refunded),0) refunded,
  COALESCE(SUM(status<>'cancelled' AND appointment_date='$today'),0) today,
  COALESCE(SUM(cancelled_by='user' OR resched_by='user'),0) cust
  FROM appointments"));
$users = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$avg   = $k['a'] > 0 ? round($k['rev'] / $k['a']) : 0;
$delta = $k['rev_l'] > 0 ? round(($k['rev_m'] - $k['rev_l']) / $k['rev_l'] * 100) : null;

// Revenue per day (last 30 days, approved bookings)
$from = date('Y-m-d', strtotime('-29 days'));
$byDay = [];
$res = mysqli_query($conn, "SELECT appointment_date d, SUM(amount) r, COUNT(*) c FROM appointments
        WHERE status='approved' AND appointment_date BETWEEN '$from' AND '$today' GROUP BY appointment_date");
while ($x = mysqli_fetch_assoc($res)) $byDay[$x['d']] = [(int) $x['r'], (int) $x['c']];
$series = [];
for ($i = 29; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $series[] = ['d' => date('j M', strtotime($d)), 'r' => $byDay[$d][0] ?? 0, 'c' => $byDay[$d][1] ?? 0];
}

// Top services
$svc = [];
foreach (mysqli_query($conn, "SELECT service, COUNT(*) c FROM appointments WHERE status<>'cancelled' GROUP BY service") as $x) {
    $n = trim(explode(' — ', $x['service'])[0]);
    $svc[$n] = ($svc[$n] ?? 0) + (int) $x['c'];
}
arsort($svc);
$svc = array_slice($svc, 0, 5, true);

// Barber workload
$bar = [];
foreach (mysqli_query($conn, "SELECT b.name, COUNT(a.id) c FROM barbers b
        LEFT JOIN appointments a ON a.barber_id=b.id AND a.status<>'cancelled'
        GROUP BY b.id, b.name ORDER BY c DESC LIMIT 6") as $x) $bar[$x['name']] = (int) $x['c'];

// Stylist leave (today onwards)
$brs = mysqli_fetch_all(mysqli_query($conn, "SELECT id, name FROM barbers ORDER BY id"), MYSQLI_ASSOC);
$leaves = mysqli_fetch_all(mysqli_query($conn, "SELECT l.barber_id, l.leave_date, b.name FROM barber_leaves l JOIN barbers b ON b.id=l.barber_id WHERE l.leave_date>='$today' ORDER BY l.leave_date, b.name"), MYSQLI_ASSOC);

// Appointments (latest 300)
$all = mysqli_fetch_all(mysqli_query($conn, "SELECT a.*, b.name AS barber FROM appointments a
        LEFT JOIN barbers b ON b.id=a.barber_id ORDER BY a.appointment_date DESC, a.id DESC LIMIT 300"), MYSQLI_ASSOC);
// Approved turns "completed" only after its 1-hour slot is over (display only, DB unchanged)
foreach ($all as &$r) {
    $r['disp'] = ($r['status'] === 'approved' && strtotime($r['appointment_date'] . ' ' . $r['appointment_time']) + 3600 <= time()) ? 'completed' : $r['status'];
}
unset($r);
$cal = [];
foreach ($all as $r) if ($r['status'] !== 'cancelled') $cal[$r['appointment_date']][] = ['t' => $r['appointment_time'], 'n' => $r['name'], 's' => $r['service'], 'b' => $r['barber'], 'st' => $r['disp']];
foreach ($cal as &$v) usort($v, function ($a, $b) { return strtotime($a['t']) - strtotime($b['t']); });
unset($v);
$todayRows = array_values(array_filter($all, function ($r) use ($today) {
    return $r['appointment_date'] === $today && $r['status'] !== 'cancelled';
}));
usort($todayRows, function ($a, $b) { return strtotime($a['appointment_time']) - strtotime($b['appointment_time']); });

/* ---------- helpers ---------- */
function who($w) { return $w === 'user' ? 'Customer' : 'Admin'; }
function inr($n) { return '₹' . number_format((int) $n); }
function barList($items, $emptyText) {
    if (!$items) { echo '<div class="muted pad">' . $emptyText . '</div>'; return; }
    $max = max(1, max($items));
    foreach ($items as $name => $c) {
        echo '<div class="hbar"><div class="hbar-top"><span>' . htmlspecialchars($name) . '</span><b>' . (int) $c . '</b></div>'
           . '<div class="hbar-track"><i style="width:' . round($c / $max * 100) . '%"></i></div></div>';
    }
}
$t  = max(1, (int) $k['t']);
$dA = $k['a'] / $t * 360; $dP = $dA + $k['p'] / $t * 360;
$donut = $k['t'] > 0
    ? "conic-gradient(#6EB478 0 {$dA}deg, #C9A66B {$dA}deg {$dP}deg, #E0665A {$dP}deg 360deg)"
    : "conic-gradient(#2a251c 0 360deg)";
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<?php
/* donut segments (presentation only) */
$C = 2 * M_PI * 54; $off = 0; $parts = [];
foreach ([[$k['a'], '#5FB57A'], [$k['p'], '#D4B073'], [$k['c'], '#E0665A']] as $sg) {
    $len = $k['t'] > 0 ? $sg[0] / $k['t'] * $C : 0;
    if ($len > 0) $parts[] = [$len, $off, $sg[1]];
    $off += $len;
}
$pct = function ($v) use ($k) { return $k['t'] > 0 ? round($v / $k['t'] * 100) : 0; };
$initial = mb_strtoupper(mb_substr($adminName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard — Deepkala Hair Salon</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap">
<link rel="icon" type="image/png" href="../assets/deepkala-emblem.png">
<style>
  :root {
    --bg:#0E0C08; --side:#120F0A; --card:#17140E; --card2:#1E1A12; --hover:#221E15;
    --line:rgba(201,166,107,.16); --line2:rgba(201,166,107,.32);
    --gold:#D4B073; --gold2:#EBD29B; --bronze:#A38A5C; --ink:#F4EFE3; --mute:#A89B82;
    --good:#5FB57A; --bad:#E0665A; --warn:#D4B073;
    --side-w:264px; --r:16px;
  }
  * { box-sizing:border-box; margin:0; padding:0; }
  html { scroll-behavior:smooth; }
  body { font-family:'Inter',system-ui,sans-serif; background:var(--bg); color:var(--ink); min-height:100vh; -webkit-font-smoothing:antialiased; }
  body::before { content:''; position:fixed; inset:0; pointer-events:none; z-index:0;
    background:radial-gradient(900px 420px at 85% -5%, rgba(201,166,107,.10), transparent 60%), radial-gradient(700px 400px at 0% 100%, rgba(169,136,76,.06), transparent 60%); }
  button { font-family:inherit; cursor:pointer; }
  a { color:inherit; }
  .ic { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; flex-shrink:0; }
  .muted { color:var(--mute); } .pad { padding:14px 0; font-size:.85rem; }
  ::-webkit-scrollbar { width:8px; height:8px; } ::-webkit-scrollbar-thumb { background:var(--line2); border-radius:8px; } ::-webkit-scrollbar-track { background:transparent; }

  /* ============ layout ============ */
  .app { position:relative; z-index:1; display:flex; min-height:100vh; }
  .side { position:fixed; inset:0 auto 0 0; width:var(--side-w); background:var(--side); border-right:1px solid var(--line);
    display:flex; flex-direction:column; padding:22px 16px; z-index:70; transition:transform .3s ease; }
  .main { flex:1; margin-left:var(--side-w); min-width:0; }

  /* ============ sidebar ============ */
  .brand { display:flex; align-items:center; gap:12px; text-decoration:none; padding:4px 8px 22px; border-bottom:1px solid var(--line); margin-bottom:18px; }
  .brand img { width:42px; height:42px; border-radius:10px; object-fit:cover; box-shadow:0 0 0 1px var(--line2); }
  .brand b { display:block; font-family:'Playfair Display',serif; font-size:1.2rem; letter-spacing:.1em; line-height:1.1; }
  .brand small { display:block; font-size:.6rem; letter-spacing:.2em; color:var(--bronze); font-weight:600; margin-top:3px; }
  .nav-t { font-size:.64rem; font-weight:700; letter-spacing:.16em; color:var(--bronze); padding:14px 12px 8px; text-transform:uppercase; }
  .nav a { display:flex; align-items:center; gap:12px; padding:11px 12px; border-radius:10px; color:var(--mute); text-decoration:none; font-size:.88rem; font-weight:500; margin-bottom:3px; position:relative; transition:background .2s, color .2s; }
  .nav a:hover { background:var(--hover); color:var(--ink); }
  .nav a.on { background:linear-gradient(90deg, rgba(212,176,115,.18), rgba(212,176,115,.04)); color:var(--gold2); }
  .nav a.on::before { content:''; position:absolute; left:-16px; top:9px; bottom:9px; width:3px; border-radius:0 4px 4px 0; background:var(--gold); }
  .nav .badge { margin-left:auto; background:var(--gold); color:var(--bg); font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:20px; }
  .nav .badge.soft { background:var(--card2); color:var(--gold); border:1px solid var(--line); }
  .side-foot { margin-top:auto; border-top:1px solid var(--line); padding-top:16px; }
  .me { display:flex; align-items:center; gap:11px; padding:10px; background:var(--card); border:1px solid var(--line); border-radius:12px; margin-top:12px; }
  .av { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,var(--gold2),var(--bronze)); color:#1a1408; font-weight:700; display:grid; place-items:center; font-family:'Playfair Display',serif; flex-shrink:0; }
  .me b { display:block; font-size:.86rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; } .me small { color:var(--mute); font-size:.72rem; }
  .scrim { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(2px); z-index:65; }

  /* ============ topbar ============ */
  .top { position:sticky; top:0; z-index:50; background:rgba(14,12,8,.8); backdrop-filter:blur(14px); border-bottom:1px solid var(--line); }
  .top-in { display:flex; align-items:center; gap:16px; padding:14px 32px; }
  .burger { display:none; background:var(--card); border:1px solid var(--line); color:var(--gold); width:40px; height:40px; border-radius:10px; place-items:center; }
  .ttl h1 { font-family:'Playfair Display',serif; font-size:1.35rem; line-height:1.15; }
  .ttl span { font-size:.76rem; color:var(--mute); }
  .gs { margin-left:auto; position:relative; width:320px; max-width:100%; }
  .gs .ic { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--bronze); width:16px; height:16px; }
  .gs input { width:100%; background:var(--card); border:1px solid var(--line); color:var(--ink); border-radius:12px; padding:10px 14px 10px 38px; font-size:.86rem; font-family:inherit; transition:.2s; }
  .gs input:focus { outline:none; border-color:var(--gold); box-shadow:0 0 0 3px rgba(212,176,115,.12); }
  .icon-btn { position:relative; width:40px; height:40px; border-radius:10px; background:var(--card); border:1px solid var(--line); color:var(--gold); display:grid; place-items:center; text-decoration:none; transition:.2s; }
  .icon-btn:hover { border-color:var(--gold); background:var(--hover); }
  .icon-btn .dot { position:absolute; top:-5px; right:-5px; background:var(--bad); color:#fff; font-size:.62rem; font-weight:700; min-width:18px; height:18px; border-radius:9px; display:grid; place-items:center; padding:0 4px; border:2px solid var(--bg); }

  .wrap { padding:28px 32px 56px; max-width:1500px; }
  section { scroll-margin-top:90px; }

  /* ============ flash ============ */
  .flash { display:flex; align-items:center; gap:10px; margin-bottom:22px; padding:13px 16px; border-radius:12px; font-size:.86rem; animation:rise .4s both; }
  .flash.good { background:rgba(95,181,122,.1); border:1px solid rgba(95,181,122,.5); color:var(--good); }
  .flash.bad  { background:rgba(224,102,90,.1); border:1px solid rgba(224,102,90,.5); color:var(--bad); }

  /* ============ hero ============ */
  .hero { display:flex; justify-content:space-between; align-items:center; gap:18px; flex-wrap:wrap; padding:26px 28px; border-radius:var(--r); margin-bottom:22px;
    background:linear-gradient(120deg, rgba(212,176,115,.16), rgba(212,176,115,.03) 55%), var(--card); border:1px solid var(--line2); position:relative; overflow:hidden; }
  .hero::after { content:''; position:absolute; right:-60px; top:-80px; width:260px; height:260px; border-radius:50%; border:1px solid var(--line); box-shadow:0 0 0 40px rgba(212,176,115,.03), 0 0 0 80px rgba(212,176,115,.02); }
  .hero h2 { font-family:'Playfair Display',serif; font-size:1.7rem; margin-bottom:6px; }
  .hero p { color:var(--mute); font-size:.9rem; max-width:520px; line-height:1.55; }
  .hero p b { color:var(--gold2); }
  .hero-cta { position:relative; z-index:1; display:flex; gap:10px; flex-wrap:wrap; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:10px; font-weight:600; font-size:.84rem; text-decoration:none; border:1px solid var(--line2); color:var(--gold); background:transparent; transition:.2s; }
  .btn:hover { background:rgba(212,176,115,.1); border-color:var(--gold); }
  .btn.solid { background:linear-gradient(135deg,var(--gold2),var(--gold)); color:#1a1408; border-color:transparent; }
  .btn.solid:hover { filter:brightness(1.07); box-shadow:0 6px 20px rgba(212,176,115,.25); }

  /* ============ cards ============ */
  .card { background:var(--card); border:1px solid var(--line); border-radius:var(--r); padding:22px; min-width:0; transition:border-color .25s, transform .25s; }
  .card:hover { border-color:var(--line2); }
  .card-h { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
  .card-h h3 { font-family:'Playfair Display',serif; font-size:1.1rem; }
  .card-h .sub { font-size:.76rem; color:var(--mute); margin-top:3px; }

  .kpis { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:22px; }
  .kpi { background:var(--card); border:1px solid var(--line); border-radius:var(--r); padding:20px; position:relative; overflow:hidden; transition:transform .25s, border-color .25s, box-shadow .25s; }
  .kpi:hover { transform:translateY(-3px); border-color:var(--line2); box-shadow:0 14px 30px rgba(0,0,0,.35); }
  .kpi-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
  .kpi label { font-size:.74rem; text-transform:uppercase; letter-spacing:.09em; color:var(--mute); font-weight:600; }
  .kpi .ico { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; background:rgba(212,176,115,.12); color:var(--gold); }
  .kpi.g .ico { background:rgba(95,181,122,.12); color:var(--good); }
  .kpi.r .ico { background:rgba(224,102,90,.12); color:var(--bad); }
  .kpi-mid { display:flex; justify-content:space-between; align-items:flex-end; gap:10px; }
  .kpi b { font-family:'Playfair Display',serif; font-size:2rem; line-height:1; color:var(--ink); }
  .kpi small { display:block; margin-top:10px; font-size:.78rem; color:var(--mute); }
  .kpi.alert { border-color:rgba(224,102,90,.45); }
  .kpi.alert b { color:#F2A097; }
  .spark { width:96px; height:38px; flex-shrink:0; }
  .up, .down { display:inline-flex; align-items:center; gap:3px; font-weight:700; font-size:.76rem; padding:2px 8px; border-radius:20px; margin-right:4px; }
  .up { color:var(--good); background:rgba(95,181,122,.12); } .down { color:var(--bad); background:rgba(224,102,90,.12); }

  .grid2 { display:grid; grid-template-columns:minmax(0,1.9fr) minmax(0,1fr); gap:16px; margin-bottom:16px; }

  /* range toggle */
  .seg { display:inline-flex; background:var(--card2); border:1px solid var(--line); border-radius:10px; padding:3px; }
  .seg button { background:transparent; color:var(--mute); border:none; padding:6px 14px; font-size:.76rem; font-weight:600; border-radius:8px; transition:.2s; }
  .seg button.on { background:var(--gold); color:#1a1408; }

  /* line chart */
  .chart { position:relative; height:270px; }
  .chart svg { width:100%; height:100%; display:block; overflow:visible; }
  .tip { position:absolute; pointer-events:none; background:#0B0905; border:1px solid var(--line2); border-radius:10px; padding:9px 12px; font-size:.76rem; min-width:128px; opacity:0; transition:opacity .15s; z-index:5; box-shadow:0 10px 24px rgba(0,0,0,.5); }
  .tip b { display:block; color:var(--gold2); font-size:.95rem; font-family:'Playfair Display',serif; margin:2px 0; } .tip span { color:var(--mute); }

  /* donut */
  .donut-box { display:flex; flex-direction:column; align-items:center; gap:22px; }
  .donut { position:relative; width:170px; height:170px; }
  .donut svg { width:100%; height:100%; }
  .donut-c { position:absolute; inset:0; display:grid; place-items:center; text-align:center; font-family:'Playfair Display',serif; font-size:2rem; line-height:1; }
  .donut-c small { display:block; font-family:'Inter',sans-serif; font-size:.68rem; color:var(--mute); margin-top:5px; letter-spacing:.1em; text-transform:uppercase; }
  .legend { width:100%; }
  .legend div { display:flex; align-items:center; gap:10px; font-size:.84rem; padding:10px 12px; border-radius:10px; background:var(--card2); margin-bottom:8px; }
  .legend div:last-child { margin-bottom:0; }
  .legend i { width:10px; height:10px; border-radius:3px; display:inline-block; }
  .legend em { font-style:normal; color:var(--mute); margin-left:auto; font-size:.76rem; }
  .legend b { min-width:26px; text-align:right; }

  /* horizontal bars */
  .hbar { margin-bottom:16px; } .hbar:last-child { margin-bottom:0; }
  .hbar-top { display:flex; justify-content:space-between; font-size:.84rem; margin-bottom:7px; gap:8px; }
  .hbar-top span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .hbar-top b { color:var(--gold2); }
  .hbar-track { height:8px; background:var(--card2); border-radius:6px; overflow:hidden; }
  .hbar-track i { display:block; height:100%; background:linear-gradient(90deg,var(--bronze),var(--gold2)); border-radius:6px; transform-origin:left; animation:grow .9s cubic-bezier(.2,.8,.2,1) both; }

  /* today timeline */
  .tl { list-style:none; max-height:290px; overflow-y:auto; position:relative; padding-right:4px; }
  .tl li { display:flex; gap:14px; align-items:center; padding:11px 0; border-bottom:1px dashed var(--line); font-size:.84rem; }
  .tl li:last-child { border-bottom:none; }
  .tl .tm { color:var(--gold2); font-weight:700; min-width:70px; font-size:.78rem; background:rgba(212,176,115,.1); padding:5px 8px; border-radius:8px; text-align:center; }
  .tl .who { flex:1; min-width:0; } .tl .who small { display:block; color:var(--mute); font-size:.74rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
  .nothing { display:grid; place-items:center; text-align:center; gap:8px; padding:34px 10px; color:var(--mute); font-size:.86rem; }
  .nothing .ic { width:34px; height:34px; color:var(--bronze); opacity:.7; }

  /* ============ appointments ============ */
  .tools { display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .tabs { display:flex; gap:6px; flex-wrap:wrap; }
  .tab { background:var(--card2); border:1px solid var(--line); color:var(--mute); padding:8px 14px; border-radius:10px; font-size:.78rem; font-weight:600; transition:.2s; }
  .tab:hover { color:var(--ink); border-color:var(--line2); }
  .tab.on { background:var(--gold); color:#1a1408; border-color:var(--gold); }
  .tab em { font-style:normal; opacity:.75; margin-left:5px; font-size:.72rem; }
  .search { background:var(--card2); border:1px solid var(--line); border-radius:10px; color:var(--ink); padding:10px 14px; font-size:.85rem; font-family:inherit; width:280px; max-width:100%; }
  .search:focus { outline:none; border-color:var(--gold); }
  .tbl { overflow-x:auto; border:1px solid var(--line); border-radius:12px; }
  table { width:100%; border-collapse:collapse; font-size:.85rem; min-width:900px; }
  th { text-align:left; color:var(--bronze); font-size:.68rem; text-transform:uppercase; letter-spacing:.1em; padding:14px 16px; background:var(--card2); border-bottom:1px solid var(--line); white-space:nowrap; font-weight:700; }
  td { padding:15px 16px; border-bottom:1px solid var(--line); vertical-align:top; }
  tr.r:last-child td { border-bottom:none; }
  tr.r { transition:background .2s; } tr.r:hover td { background:rgba(212,176,115,.045); }
  tr.r[data-s="pending"] td:first-child { box-shadow:inset 3px 0 0 var(--gold); }
  small { color:var(--mute); }
  .cu { display:flex; align-items:center; gap:11px; }
  .cu .av { width:34px; height:34px; font-size:.85rem; } .cu b { font-weight:600; display:block; }
  .id { color:var(--bronze); font-weight:600; font-size:.8rem; }
  .amt { font-weight:700; color:var(--gold2); }
  .chip { display:inline-flex; align-items:center; gap:6px; padding:4px 11px; border-radius:20px; font-size:.72rem; font-weight:600; }
  .chip::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
  .pending { background:rgba(212,176,115,.14); color:var(--gold); }
  .approved { background:rgba(95,181,122,.14); color:var(--good); }
  .cancelled { background:rgba(224,102,90,.14); color:var(--bad); }
  .lv { display:flex; gap:10px; flex-wrap:wrap; align-items:center; } .lv select, .lv input { background:var(--card2); border:1px solid var(--line); color:var(--ink); border-radius:10px; padding:9px 12px; font-family:inherit; font-size:.84rem; color-scheme:dark; }
  .lvl { display:flex; gap:8px; flex-wrap:wrap; margin-top:14px; } .lvl form { display:inline-flex; align-items:center; gap:8px; background:var(--card2); border:1px solid var(--line); border-radius:20px; padding:4px 6px 4px 12px; font-size:.78rem; } .lvl button { background:none; border:none; color:var(--bad); font-size:1rem; line-height:1; }
  .completed { background:rgba(122,162,224,.14); color:#8FB4EA; }
  /* upcoming calendar */
  .cal-wrap { display:grid; grid-template-columns:minmax(0,1.5fr) minmax(0,1fr); gap:24px; }
  .cal-nav { display:flex; align-items:center; gap:10px; } .cal-nav b { min-width:140px; text-align:center; font-family:'Playfair Display',serif; }
  .cal-g { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; }
  .cal-g i { font-style:normal; text-align:center; color:var(--bronze); font-size:.66rem; text-transform:uppercase; letter-spacing:.08em; padding:4px 0 8px; }
  .cal-g .d { position:relative; aspect-ratio:1; min-height:44px; background:var(--card2); border:1px solid var(--line); border-radius:10px; color:var(--ink); font-size:.86rem; font-weight:600; transition:.2s; }
  .cal-g .d:hover { border-color:var(--gold); }
  .cal-g .d.past { opacity:.5; } .cal-g .d.today { border-color:var(--gold); box-shadow:inset 0 0 0 1px var(--gold); }
  .cal-g .d.on { background:var(--gold); color:#1a1408; border-color:var(--gold); opacity:1; }
  .cal-g .d em { position:absolute; right:5px; bottom:4px; font-style:normal; font-size:.62rem; font-weight:700; background:var(--gold); color:#1a1408; border-radius:8px; padding:1px 6px; }
  .cal-g .d.on em { background:#1a1408; color:var(--gold2); }
  .cal-day h4 { font-family:'Playfair Display',serif; font-size:1.05rem; margin-bottom:12px; }
  .cal-day ul { list-style:none; display:grid; gap:10px; max-height:340px; overflow-y:auto; }
  .cal-day li { display:flex; gap:12px; align-items:center; padding:11px 12px; background:var(--card2); border:1px solid var(--line); border-radius:10px; }
  .cal-day .tm { color:var(--gold2); font-weight:700; font-size:.8rem; min-width:68px; }
  .cal-day .who { flex:1; min-width:0; font-size:.86rem; } .cal-day .who small { display:block; margin-top:2px; }
  @media (max-width:900px) { .cal-wrap { grid-template-columns:1fr; } }
  .note { margin-top:9px; font-size:.74rem; color:var(--mute); line-height:1.55; max-width:260px; padding-left:10px; border-left:2px solid var(--line2); }
  .note b { color:var(--ink); }
  .cust { background:var(--gold); color:#1a1408; border-radius:4px; padding:1px 6px; font-size:.6rem; font-weight:700; letter-spacing:.04em; }
  .b { padding:8px 13px; border-radius:8px; font-weight:600; font-size:.76rem; border:none; transition:.2s; }
  .ok { background:linear-gradient(135deg,var(--gold2),var(--gold)); color:#1a1408; } .ok:hover { filter:brightness(1.08); }
  .alt { background:transparent; color:var(--gold); border:1px solid var(--line2); } .alt:hover { background:rgba(212,176,115,.1); }
  .no { background:transparent; color:var(--bad); border:1px solid rgba(224,102,90,.5); } .no:hover { background:rgba(224,102,90,.1); }
  .acts { display:flex; gap:6px; flex-wrap:wrap; }
  .empty { padding:46px; text-align:center; color:var(--mute); display:none; }
  .more { display:block; margin:18px auto 0; }

  /* ============ modal ============ */
  .ov { display:none; position:fixed; inset:0; background:rgba(0,0,0,.72); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:20px; z-index:90; }
  .ov.show { display:flex; }
  .modal { background:var(--card); border:1px solid var(--line2); border-radius:18px; padding:26px; width:100%; max-width:430px; max-height:92vh; overflow-y:auto; box-shadow:0 30px 70px rgba(0,0,0,.6); animation:pop .25s both; }
  .modal h2 { font-family:'Playfair Display',serif; font-size:1.3rem; margin-bottom:6px; }
  .modal label { display:block; font-size:.78rem; color:var(--gold); margin:14px 0 7px; font-weight:600; }
  .modal input, .modal select, .modal textarea { width:100%; padding:11px 12px; border-radius:10px; border:1px solid var(--line2); background:var(--card2); color:var(--ink); font-family:inherit; font-size:.9rem; }
  .modal input:focus, .modal select:focus, .modal textarea:focus { outline:none; border-color:var(--gold); }
  .modal .row { display:flex; gap:10px; margin-top:20px; }
  .modal .row .b { flex:1; padding:11px; }


  /* ---- polish pass ---- */
  .kpi::before { content:''; position:absolute; left:0; right:0; top:0; height:3px; background:linear-gradient(90deg,var(--bronze),var(--gold2)); opacity:.85; }
  .kpi.g::before { background:linear-gradient(90deg,#3f8f5a,var(--good)); } .kpi.r::before, .kpi.alert::before { background:linear-gradient(90deg,#b4483e,var(--bad)); }
  .kpi .ico { width:44px; height:44px; } .kpi b { font-size:2.15rem; letter-spacing:-.01em; }
  .card { box-shadow:0 10px 28px rgba(0,0,0,.28); }
  .card-h h3::after { content:''; display:block; width:28px; height:2px; margin-top:7px; border-radius:2px; background:var(--gold); }
  th { position:sticky; top:0; z-index:1; }
  tr.r:nth-child(even) td { background:rgba(255,255,255,.012); }
  .hero { box-shadow:0 14px 34px rgba(0,0,0,.35); }

  .grid2.eq { grid-template-columns:repeat(2,minmax(0,1fr)); }
  #today .tl { max-height:none; } #today .tl li { padding:16px 4px; font-size:.92rem; } #today .tl .tm { min-width:84px; font-size:.84rem; padding:7px 10px; }
  #today .card { max-width:820px; } #today .nothing { padding:60px 10px; }

  /* ============ animation ============ */
  @keyframes rise { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:none; } }
  @keyframes grow { from { transform:scaleX(0); } to { transform:scaleX(1); } }
  @keyframes pop { from { opacity:0; transform:scale(.95) translateY(8px); } to { opacity:1; transform:none; } }
  .rise { animation:rise .55s cubic-bezier(.2,.8,.2,1) both; animation-delay:calc(var(--i,0) * 70ms); }
  @media (prefers-reduced-motion:reduce) { * { animation:none !important; transition:none !important; scroll-behavior:auto !important; } }

  /* ============ responsive ============ */
  @media (max-width:1200px) { .kpis { grid-template-columns:repeat(2,1fr); } .grid3 { grid-template-columns:repeat(2,minmax(0,1fr)); } .grid3 .card:last-child { grid-column:1 / -1; } }
  @media (max-width:1000px) {
    .side { transform:translateX(-100%); box-shadow:20px 0 50px rgba(0,0,0,.5); }
    .side.open { transform:none; } .scrim.show { display:block; }
    .main { margin-left:0; } .burger { display:grid; }
    .grid2 { grid-template-columns:1fr; }
    .top-in { padding:12px 18px; } .wrap { padding:20px 18px 48px; }
    .nav a.on::before { left:-16px; }
  }
  @media (max-width:760px) {
    .ttl span, .gs { display:none; } .top-in { gap:12px; } .ttl { flex:1; } .ttl h1 { font-size:1.15rem; }
    .hero { padding:20px; } .hero h2 { font-size:1.35rem; }
    .tools { flex-direction:column; align-items:stretch; } .search { width:100%; }
    .tbl { border:none; overflow:visible; }
    table { min-width:0; } thead { display:none; } table, tbody, tr, td { display:block; width:100%; }
    tr.r { background:var(--card2); border:1px solid var(--line); border-radius:14px; margin-bottom:12px; padding:6px 0; }
    tr.r[data-s="pending"] { border-color:rgba(212,176,115,.5); } tr.r[data-s="pending"] td:first-child { box-shadow:none; }
    td { display:flex; justify-content:space-between; align-items:flex-start; gap:14px; padding:9px 16px; border:none; text-align:right; }
    td::before { content:attr(data-label); color:var(--bronze); font-size:.66rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; padding-top:3px; text-align:left; flex-shrink:0; }
    td > * { text-align:right; } .cu { flex-direction:row-reverse; } .cu div { text-align:right; } .note { text-align:left; margin-left:auto; }
    .acts { justify-content:flex-end; }
  }
  @media (max-width:520px) { .kpis { grid-template-columns:1fr; gap:12px; } .kpi b { font-size:1.75rem; } .chart { height:230px; } .hero-cta { width:100%; } .hero-cta .btn { flex:1; justify-content:center; } }
</style>
</head>
<body>

<!-- icon sprite -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
  <symbol id="i-dash" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></symbol>
  <symbol id="i-trend" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></symbol>
  <symbol id="i-pie" viewBox="0 0 24 24"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></symbol>
  <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
  <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m9 16 2 2 4-4"/></symbol>
  <symbol id="i-rupee" viewBox="0 0 24 24"><path d="M6 3h12M6 8h12M6 13l8.5 8M6 13h3M9 13c6.667 0 6.667-10 0-10"/></symbol>
  <symbol id="i-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
  <symbol id="i-hourglass" viewBox="0 0 24 24"><path d="M5 22h14M5 2h14M17 22v-4.17a2 2 0 0 0-.59-1.42L12 12l-4.41 4.41A2 2 0 0 0 7 17.83V22M7 2v4.17a2 2 0 0 0 .59 1.42L12 12l4.41-4.41A2 2 0 0 0 17 6.17V2"/></symbol>
  <symbol id="i-refund" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></symbol>
  <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></symbol>
  <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
  <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
  <symbol id="i-out" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><path d="M21 12H9"/></symbol>
  <symbol id="i-ext" viewBox="0 0 24 24"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></symbol>
  <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
  <symbol id="i-alert" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
  <symbol id="i-empty" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
</defs></svg>

<div class="scrim" id="scrim"></div>
<div class="app">

  <!-- ================= SIDEBAR ================= -->
  <aside class="side" id="side">
    <a href="index.php" class="brand" aria-label="Deepkala Admin Dashboard">
      <img src="../assets/deepkala-emblem.png" alt="Deepkala Hair Salon">
      <div><b>DEEPKALA</b><small>ADMIN PANEL</small></div>
    </a>

    <nav class="nav" id="nav">
      <div class="nav-t">Menu</div>
      <a href="#dash" class="on"><svg class="ic"><use href="#i-dash"/></svg>Dashboard</a>
      <a href="#today"><svg class="ic"><use href="#i-clock"/></svg>Today's Schedule<span class="badge soft"><?= count($todayRows) ?></span></a>
      <a href="#appts"><svg class="ic"><use href="#i-cal"/></svg>Appointments<?php if ($k['p'] > 0) { ?><span class="badge"><?= (int) $k['p'] ?></span><?php } ?></a>
    </nav>

    <div class="side-foot">
      <nav class="nav">
        <a href="../index.php" target="_blank" rel="noopener"><svg class="ic"><use href="#i-ext"/></svg>View website</a>
        <a href="../logout.php"><svg class="ic"><use href="#i-out"/></svg>Logout</a>
      </nav>
      <div class="me"><div class="av"><?= htmlspecialchars($initial) ?></div><div style="min-width:0"><b><?= htmlspecialchars($adminName) ?></b><small>Administrator</small></div></div>
    </div>
  </aside>

  <!-- ================= MAIN ================= -->
  <div class="main">
    <header class="top"><div class="top-in">
      <button class="burger" id="burger" aria-label="Open menu"><svg class="ic"><use href="#i-menu"/></svg></button>
      <div class="ttl"><h1>Dashboard</h1><span><?= date('l, j F Y') ?></span></div>
      <div class="gs"><svg class="ic"><use href="#i-search"/></svg><input id="gq" type="search" placeholder="Search bookings, customers, services…" autocomplete="off"></div>
      <a class="icon-btn" href="#appts" id="bell" title="Pending approvals" aria-label="Pending approvals"><svg class="ic"><use href="#i-bell"/></svg><?php if ($k['p'] > 0) { ?><span class="dot"><?= (int) $k['p'] ?></span><?php } ?></a>
      <div class="av" title="<?= htmlspecialchars($adminName) ?>"><?= htmlspecialchars($initial) ?></div>
    </div></header>

    <div class="wrap">
      <?php if ($flash) { ?><div class="flash <?= $flash[0] ?>"><svg class="ic"><use href="#<?= $flash[0] === 'good' ? 'i-check' : 'i-alert' ?>"/></svg><?= htmlspecialchars($flash[1]) ?></div><?php } ?>

      <!-- Overview -->
      <section id="overview" data-view="dash">
        <div class="hero rise">
          <div>
            <h2>Welcome back, <?= htmlspecialchars($adminName) ?> 👋</h2>
            <p>You have <b><?= (int) $k['today'] ?> booking<?= $k['today'] == 1 ? '' : 's' ?></b> today and <b><?= (int) $k['p'] ?> awaiting approval</b>. Here's how Deepkala is performing.</p>
          </div>
          <div class="hero-cta">
            <a class="btn solid" href="#appts" id="heroPending"><svg class="ic"><use href="#i-hourglass"/></svg>Review pending</a>
            <a class="btn" href="#today"><svg class="ic"><use href="#i-clock"/></svg>Today's schedule</a>
          </div>
        </div>

        <div class="kpis">
          <div class="kpi rise" style="--i:1"><div class="kpi-top"><label>Total revenue</label><div class="ico"><svg class="ic"><use href="#i-rupee"/></svg></div></div>
            <div class="kpi-mid"><b><?= inr($k['rev']) ?></b><svg class="spark" data-k="r"></svg></div>
            <small><?= (int) $k['a'] ?> approved bookings · avg <?= inr($avg) ?></small></div>

          <div class="kpi rise" style="--i:2"><div class="kpi-top"><label>This month</label><div class="ico"><svg class="ic"><use href="#i-trend"/></svg></div></div>
            <div class="kpi-mid"><b><?= inr($k['rev_m']) ?></b></div>
            <small><?php if ($delta === null) echo 'No data for last month'; else echo '<span class="' . ($delta >= 0 ? 'up">▲ ' : 'down">▼ ') . abs($delta) . '%</span>vs last month'; ?></small></div>

          <div class="kpi g rise" style="--i:3"><div class="kpi-top"><label>Today's bookings</label><div class="ico"><svg class="ic"><use href="#i-cal"/></svg></div></div>
            <div class="kpi-mid"><b><?= (int) $k['today'] ?></b><svg class="spark" data-k="c"></svg></div>
            <small>Scheduled for today</small></div>

          <div class="kpi <?= $k['p'] > 0 ? 'alert' : '' ?> rise" style="--i:4"><div class="kpi-top"><label>Awaiting approval</label><div class="ico" style="<?= $k['p'] > 0 ? 'background:rgba(224,102,90,.12);color:var(--bad)' : '' ?>"><svg class="ic"><use href="#i-hourglass"/></svg></div></div>
            <div class="kpi-mid"><b><?= (int) $k['p'] ?></b></div>
            <small><?= inr($k['pend_val']) ?> pending value</small></div>

          <div class="kpi rise" style="--i:5"><div class="kpi-top"><label>Customers</label><div class="ico"><svg class="ic"><use href="#i-users"/></svg></div></div>
            <div class="kpi-mid"><b><?= $users ?></b></div>
            <small><?= (int) $k['cust'] ?> bookings changed by customers</small></div>

          <div class="kpi r rise" style="--i:6"><div class="kpi-top"><label>Refunded to wallets</label><div class="ico"><svg class="ic"><use href="#i-refund"/></svg></div></div>
            <div class="kpi-mid"><b><?= inr($k['refunded']) ?></b></div>
            <small><?= (int) $k['c'] ?> cancelled bookings</small></div>
        </div>
      </section>

      <!-- Revenue + status -->
      <section id="revenue" data-view="dash">
        <div class="grid2">
          <div class="card rise" style="--i:2">
            <div class="card-h">
              <div><h3>Revenue trend</h3><div class="sub" id="revSum"></div></div>
              <div class="seg" id="rng"><button data-n="7">7 days</button><button data-n="30" class="on">30 days</button></div>
            </div>
            <div class="chart" id="chart"><div class="tip" id="tip"></div></div>
          </div>

          <div class="card rise" style="--i:3">
            <div class="card-h"><div><h3>Bookings by status</h3><div class="sub">All time</div></div></div>
            <div class="donut-box">
              <div class="donut">
                <svg viewBox="0 0 140 140">
                  <circle cx="70" cy="70" r="54" fill="none" stroke="#221E15" stroke-width="16"/>
                  <?php foreach ($parts as $p) { ?>
                  <circle cx="70" cy="70" r="54" fill="none" stroke="<?= $p[2] ?>" stroke-width="16" stroke-linecap="butt"
                    stroke-dasharray="<?= round(max(0, $p[0] - ($k['t'] > 0 && count($parts) > 1 ? 2 : 0)), 2) ?> <?= round($C, 2) ?>" stroke-dashoffset="<?= -round($p[1], 2) ?>" transform="rotate(-90 70 70)"/>
                  <?php } ?>
                </svg>
                <div class="donut-c"><div><?= (int) $k['t'] ?><small>total</small></div></div>
              </div>
              <div class="legend">
                <div><i style="background:#5FB57A"></i>Approved<em><?= $pct($k['a']) ?>%</em><b><?= (int) $k['a'] ?></b></div>
                <div><i style="background:#D4B073"></i>Pending<em><?= $pct($k['p']) ?>%</em><b><?= (int) $k['p'] ?></b></div>
                <div><i style="background:#E0665A"></i>Cancelled<em><?= $pct($k['c']) ?>%</em><b><?= (int) $k['c'] ?></b></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Insights -->
      <section id="insights" data-view="dash">
        <div class="grid2 eq">
          <div class="card rise" style="--i:2"><div class="card-h"><div><h3>Top services</h3><div class="sub">by bookings</div></div></div><?php barList($svc, 'No bookings yet.'); ?></div>
          <div class="card rise" style="--i:3"><div class="card-h"><div><h3>Barber workload</h3><div class="sub">active bookings</div></div></div><?php barList($bar, 'No barbers yet.'); ?></div>
        </div>
      </section>

      <!-- Today -->
      <section id="today" data-view="today">
        <div class="card rise" style="--i:4"><div class="card-h"><div><h3>Today's schedule</h3><div class="sub"><?= count($todayRows) ?> appointment<?= count($todayRows) == 1 ? '' : 's' ?></div></div></div>
            <?php if (!$todayRows) { ?><div class="nothing"><svg class="ic"><use href="#i-empty"/></svg>Nothing scheduled for today.</div><?php } else { ?>
            <ul class="tl"><?php foreach ($todayRows as $r) { ?>
              <li><span class="tm"><?= htmlspecialchars($r['appointment_time']) ?></span>
                <span class="who"><?= htmlspecialchars($r['name']) ?><small><?= htmlspecialchars(explode(' — ', $r['service'])[0]) ?> · <?= htmlspecialchars($r['barber']) ?></small></span>
                <span class="chip <?= $r['disp'] ?>"><?= ucfirst($r['disp']) ?></span></li>
            <?php } ?></ul><?php } ?>
          </div>
      </section>

      <!-- Appointments -->
      <section id="appts" data-view="appts">
        <div class="card rise" style="--i:1;margin-bottom:22px">
          <div class="card-h"><div><h3>Upcoming appointments</h3><div class="sub">Click a day to see its bookings</div></div>
            <div class="cal-nav"><button class="b alt" id="cPrev" aria-label="Previous month">&lsaquo;</button><b id="cMonth"></b><button class="b alt" id="cNext" aria-label="Next month">&rsaquo;</button></div></div>
          <div class="cal-wrap"><div class="cal-g" id="calGrid"></div><div class="cal-day" id="calDay"></div></div>
        </div>
        <div class="card rise" style="--i:1;margin-bottom:22px">
          <div class="card-h"><div><h3>Stylist leave</h3><div class="sub">Customers can't book a stylist on their leave day</div></div></div>
          <form class="lv" method="post">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="hidden" name="action" value="leave_add">
            <select name="barber" required><option value="">Stylist…</option><?php foreach ($brs as $b) { ?><option value="<?= (int) $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option><?php } ?></select>
            <input type="date" name="date" min="<?= $today ?>" required>
            <button class="b ok">Mark leave</button>
          </form>
          <?php if ($leaves) { ?><div class="lvl"><?php foreach ($leaves as $l) { ?>
            <form method="post"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>"><input type="hidden" name="action" value="leave_del"><input type="hidden" name="barber" value="<?= (int) $l['barber_id'] ?>"><input type="hidden" name="date" value="<?= $l['leave_date'] ?>">
              <span><?= htmlspecialchars($l['name']) ?> · <?= date('j M', strtotime($l['leave_date'])) ?></span><button title="Remove leave" aria-label="Remove leave">&times;</button></form>
          <?php } ?></div><?php } ?>
        </div>
        <div class="card rise" style="--i:2">
          <div class="card-h"><div><h3>Appointments</h3><div class="sub">Latest 300 · approve, reschedule or cancel</div></div></div>
          <div class="tools">
            <div class="tabs" id="tabs">
              <button class="tab on" data-f="all">All<em><?= (int) $k['t'] ?></em></button>
              <button class="tab" data-f="pending">Pending<em><?= (int) $k['p'] ?></em></button>
              <button class="tab" data-f="approved">Approved<em><?= (int) ($k['a'] - $k['done']) ?></em></button>
              <button class="tab" data-f="completed">Completed<em><?= (int) $k['done'] ?></em></button>
              <button class="tab" data-f="cancelled">Cancelled<em><?= (int) $k['c'] ?></em></button>
              <button class="tab" data-f="up">Upcoming</button>
            </div>
            <input class="search" id="q" type="search" placeholder="Search name, phone, service, #id…">
          </div>
          <div class="tbl">
            <table>
              <thead><tr><th>ID</th><th>Customer</th><th>Service</th><th>Date &amp; Time</th><th>Barber</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
              <tbody id="tbody">
              <?php foreach ($all as $r) {
                  $up = ($r['appointment_date'] >= $today && !in_array($r['disp'], ['cancelled', 'completed'])) ? '1' : '0';
                  $q  = strtolower($r['name'] . ' ' . $r['phone'] . ' ' . $r['service'] . ' #' . $r['id'] . ' ' . $r['barber']);
              ?>
              <tr class="r" data-s="<?= $r['disp'] ?>" data-up="<?= $up ?>" data-q="<?= htmlspecialchars($q) ?>">
                <td data-label="ID"><span class="id">#<?= $r['id'] ?></span></td>
                <td data-label="Customer"><div class="cu"><div class="av"><?= htmlspecialchars(mb_strtoupper(mb_substr($r['name'], 0, 1))) ?></div><div><b><?= htmlspecialchars($r['name']) ?></b><small><?= htmlspecialchars($r['phone']) ?></small></div></div></td>
                <td data-label="Service"><?= htmlspecialchars($r['service']) ?></td>
                <td data-label="Date &amp; Time"><div><?= date('D, j M Y', strtotime($r['appointment_date'])) ?><br><small><?= htmlspecialchars($r['appointment_time']) ?></small></div></td>
                <td data-label="Barber"><?= htmlspecialchars($r['barber']) ?></td>
                <td data-label="Amount"><div><span class="amt"><?= inr($r['amount']) ?></span><br><small><?= htmlspecialchars($r['payment_method']) ?></small></div></td>
                <td data-label="Status">
                  <div>
                  <span class="chip <?= $r['disp'] ?>"><?= ucfirst($r['disp']) ?></span>
                  <?php if ($r['status'] === 'cancelled') { ?>
                    <div class="note"><b>Cancelled by <?= who($r['cancelled_by']) ?></b> <?= $r['cancelled_by'] === 'user' ? '<span class="cust">CUSTOMER</span>' : '' ?><br>
                      Reason: <?= htmlspecialchars($r['admin_note'] ?: '—') ?><br>
                      <?= (int) $r['refunded'] > 0 ? '₹' . (int) $r['refunded'] . ' refunded to wallet' : '' ?></div>
                  <?php } elseif ((int) $r['rescheduled_count'] > 0) { ?>
                    <div class="note"><b>Rescheduled by <?= who($r['resched_by']) ?></b> <?= $r['resched_by'] === 'user' ? '<span class="cust">CUSTOMER</span>' : '' ?><br>
                      Reason: <?= htmlspecialchars($r['resched_note'] ?: '—') ?><br>
                      Was: <?= htmlspecialchars($r['old_slot']) ?></div>
                  <?php } ?>
                  </div>
                </td>
                <td data-label="Action">
                  <?php if (!in_array($r['disp'], ['cancelled', 'completed'])) { ?>
                  <div class="acts">
                    <?php if ($r['status'] == 'pending') { ?>
                    <form method="post">
                      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                      <input type="hidden" name="id" value="<?= $r['id'] ?>">
                      <button class="b ok" name="action" value="approve">Approve</button>
                    </form>
                    <?php } ?>
                    <button class="b alt" onclick="openM('reschedule', <?= $r['id'] ?>)">Reschedule</button>
                    <button class="b no" onclick="openM('cancel', <?= $r['id'] ?>)">Cancel</button>
                  </div>
                  <?php } ?>
                </td>
              </tr>
              <?php } ?>
              </tbody>
            </table>
            <div class="empty" id="empty">No appointments match your filter.</div>
          </div>
          <button class="b alt more" id="more" style="display:none">Show more</button>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- Reschedule / cancel modal -->
<div class="ov" id="ov">
  <form class="modal" method="post">
    <h2 id="mTitle"></h2>
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
    <input type="hidden" name="id" id="mId">
    <input type="hidden" name="action" id="mAction">
    <div id="mWhen">
      <label>New date</label>
      <input type="date" name="date" id="mDate" min="<?= date('Y-m-d') ?>">
      <label>New time</label>
      <select name="time"><?php foreach (DK_SLOTS as $t2) echo "<option>$t2</option>"; ?></select>
    </div>
    <label>Reason (customer will see this) *</label>
    <textarea name="reason" rows="3" maxlength="250" required placeholder="e.g. Stylist is on leave that day"></textarea>
    <div class="row">
      <button type="button" class="b alt" onclick="closeM()">Back</button>
      <button type="submit" class="b ok" id="mGo"></button>
    </div>
  </form>
</div>

<script>
/* ---- sidebar (mobile) ---- */
var side = document.getElementById('side'), scrim = document.getElementById('scrim');
function toggleSide(open) { side.classList.toggle('open', open); scrim.classList.toggle('show', open); }
document.getElementById('burger').addEventListener('click', function () { toggleSide(true); });
scrim.addEventListener('click', function () { toggleSide(false); });
document.getElementById('nav').addEventListener('click', function (e) { if (e.target.closest('a')) toggleSide(false); });

/* ---- views: one section group at a time, chosen by #hash ---- */
var links = document.querySelectorAll('#nav a'), TITLES = { dash:'Dashboard', today:"Today's Schedule", appts:'Appointments' };
function route() {
  var v = location.hash.slice(1); if (!TITLES[v]) v = 'dash';
  document.querySelectorAll('[data-view]').forEach(function (el) { el.hidden = el.dataset.view !== v; });
  links.forEach(function (a) { a.classList.toggle('on', a.getAttribute('href') === '#' + v); });
  document.querySelector('.ttl h1').textContent = TITLES[v]; window.scrollTo(0, 0);
  if (v === 'dash') drawChart(curN); // chart measures its box, so redraw once visible
}
window.addEventListener('hashchange', route);

/* ---- modal ---- */
function openM(kind, id) {
  var r = kind === 'reschedule';
  document.getElementById('mTitle').textContent = (r ? 'Reschedule' : 'Cancel') + ' appointment #' + id;
  document.getElementById('mGo').textContent = r ? 'Reschedule' : 'Cancel & refund to wallet';
  document.getElementById('mAction').value = kind;
  document.getElementById('mId').value = id;
  document.getElementById('mWhen').style.display = r ? 'block' : 'none';
  document.getElementById('mDate').required = r;
  document.getElementById('ov').classList.add('show');
}
function closeM() { document.getElementById('ov').classList.remove('show'); }
document.getElementById('ov').addEventListener('click', function (e) { if (e.target === this) closeM(); });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeM(); toggleSide(false); } });

/* ---- charts (pure SVG, no library needed) ---- */
var DATA = <?= json_encode($series, JSON_HEX_TAG) ?>;
var NS = 'http://www.w3.org/2000/svg', curN = 30;
function money(n) { return '₹' + Number(n).toLocaleString('en-IN'); }
function short(n) { return n >= 100000 ? '₹' + (n / 100000).toFixed(1).replace('.0', '') + 'L' : n >= 1000 ? '₹' + (n / 1000).toFixed(1).replace('.0', '') + 'k' : '₹' + n; }
function niceMax(v) { if (v <= 0) return 100; var p = Math.pow(10, Math.floor(Math.log10(v))), f = v / p; return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10) * p; }
function smooth(pts) {
  var d = 'M' + pts[0][0] + ',' + pts[0][1];
  for (var i = 1; i < pts.length; i++) { var mx = (pts[i - 1][0] + pts[i][0]) / 2; d += ' C' + mx + ',' + pts[i - 1][1] + ' ' + mx + ',' + pts[i][1] + ' ' + pts[i][0] + ',' + pts[i][1]; }
  return d;
}
function drawChart(n) {
  curN = n;
  var box = document.getElementById('chart'), tip = document.getElementById('tip');
  var d = DATA.slice(-n), W = box.clientWidth, H = box.clientHeight;
  var L = 52, R = 10, T = 12, B = 28, iw = W - L - R, ih = H - T - B;
  var max = niceMax(Math.max.apply(null, d.map(function (x) { return x.r; }).concat([1])));
  var total = 0, cnt = 0; d.forEach(function (x) { total += x.r; cnt += x.c; });
  var X = function (i) { return L + (d.length === 1 ? iw / 2 : i * iw / (d.length - 1)); }, Y = function (v) { return T + ih - v / max * ih; };
  var pts = d.map(function (x, i) { return [X(i), Y(x.r)]; });
  var s = '<defs><linearGradient id="ga" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#D4B073" stop-opacity=".38"/><stop offset="1" stop-color="#D4B073" stop-opacity="0"/></linearGradient>' +
          '<linearGradient id="gl" x1="0" x2="1"><stop offset="0" stop-color="#A38A5C"/><stop offset="1" stop-color="#EBD29B"/></linearGradient></defs>';
  for (var g = 0; g <= 4; g++) {
    var gy = T + ih - g / 4 * ih;
    s += '<line x1="' + L + '" x2="' + (W - R) + '" y1="' + gy + '" y2="' + gy + '" stroke="rgba(201,166,107,' + (g ? .12 : .28) + ')" ' + (g ? 'stroke-dasharray="4 5"' : '') + '/>' +
         '<text x="' + (L - 10) + '" y="' + (gy + 4) + '" text-anchor="end" fill="#A89B82" font-size="10.5" font-family="Inter">' + (g ? short(Math.round(max * g / 4)) : '₹0') + '</text>';
  }
  var idx = n === 7 ? [0,1,2,3,4,5,6] : [0, 7, 14, 21, 29];
  idx.forEach(function (i) { if (d[i]) s += '<text x="' + X(i) + '" y="' + (H - 6) + '" text-anchor="' + (i === 0 ? 'start' : i === d.length - 1 ? 'end' : 'middle') + '" fill="#A89B82" font-size="10.5" font-family="Inter">' + d[i].d + '</text>'; });
  var line = smooth(pts);
  s += '<path d="' + line + ' L' + pts[pts.length - 1][0] + ',' + (T + ih) + ' L' + pts[0][0] + ',' + (T + ih) + ' Z" fill="url(#ga)"/>' +
       '<path id="ln" d="' + line + '" fill="none" stroke="url(#gl)" stroke-width="2.6" stroke-linecap="round"/>' +
       '<line id="cx" y1="' + T + '" y2="' + (T + ih) + '" stroke="rgba(235,210,155,.45)" stroke-dasharray="3 4" opacity="0"/>' +
       '<circle id="cd" r="5.5" fill="#17140E" stroke="#EBD29B" stroke-width="2.4" opacity="0"/>' +
       '<rect id="hit" x="' + L + '" y="' + T + '" width="' + iw + '" height="' + ih + '" fill="transparent"/>';
  var old = box.querySelector('svg'); if (old) old.remove();
  var svg = document.createElementNS(NS, 'svg'); svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H); svg.innerHTML = s; box.insertBefore(svg, tip);
  var ln = svg.querySelector('#ln'), len = ln.getTotalLength();
  ln.style.strokeDasharray = len; ln.style.strokeDashoffset = len; ln.getBoundingClientRect();
  ln.style.transition = 'stroke-dashoffset 1s ease'; ln.style.strokeDashoffset = 0;
  var cx = svg.querySelector('#cx'), cd = svg.querySelector('#cd'), hit = svg.querySelector('#hit');
  function show(ev) {
    var r = svg.getBoundingClientRect(), mx = (ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left;
    var i = Math.max(0, Math.min(d.length - 1, Math.round((mx - L) / (iw / Math.max(1, d.length - 1)))));
    cx.setAttribute('x1', X(i)); cx.setAttribute('x2', X(i)); cd.setAttribute('cx', X(i)); cd.setAttribute('cy', Y(d[i].r));
    cx.setAttribute('opacity', 1); cd.setAttribute('opacity', 1);
    tip.innerHTML = '<span>' + d[i].d + '</span><b>' + money(d[i].r) + '</b><span>' + d[i].c + ' approved booking' + (d[i].c == 1 ? '' : 's') + '</span>';
    var tx = X(i) + 14; if (tx + 140 > W) tx = X(i) - 146;
    tip.style.left = tx + 'px'; tip.style.top = Math.max(0, Y(d[i].r) - 30) + 'px'; tip.style.opacity = 1;
  }
  function hide() { cx.setAttribute('opacity', 0); cd.setAttribute('opacity', 0); tip.style.opacity = 0; }
  hit.addEventListener('mousemove', show); hit.addEventListener('touchmove', show, { passive: true }); hit.addEventListener('touchstart', show, { passive: true });
  hit.addEventListener('mouseleave', hide); hit.addEventListener('touchend', hide);
  document.getElementById('revSum').textContent = money(total) + ' from ' + cnt + ' approved booking' + (cnt == 1 ? '' : 's') + ' in the last ' + n + ' days';
}
document.getElementById('rng').addEventListener('click', function (e) {
  var b = e.target.closest('button'); if (!b) return;
  this.querySelectorAll('button').forEach(function (x) { x.classList.remove('on'); });
  b.classList.add('on'); drawChart(+b.dataset.n);
});
var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { drawChart(curN); }, 150); });
drawChart(30); route();

/* sparklines on KPI cards */
document.querySelectorAll('.spark').forEach(function (el) {
  var k = el.dataset.k, v = DATA.map(function (x) { return x[k]; }), m = Math.max.apply(null, v.concat([1])), w = 96, h = 38;
  var pts = v.map(function (y, i) { return [i * w / (v.length - 1), h - 4 - y / m * (h - 8)]; }), dd = smooth(pts);
  var col = k === 'c' ? '#5FB57A' : '#D4B073';
  el.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
  el.innerHTML = '<defs><linearGradient id="sp' + k + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + col + '" stop-opacity=".35"/><stop offset="1" stop-color="' + col + '" stop-opacity="0"/></linearGradient></defs>' +
    '<path d="' + dd + ' L' + w + ',' + h + ' L0,' + h + ' Z" fill="url(#sp' + k + ')"/><path d="' + dd + '" fill="none" stroke="' + col + '" stroke-width="2" stroke-linecap="round"/>';
});

/* ---- upcoming calendar ---- */
var CAL = <?= json_encode($cal, JSON_HEX_TAG) ?>, TODAY = '<?= $today ?>';
var cy = +TODAY.slice(0, 4), cm = +TODAY.slice(5, 7) - 1, sel = TODAY;
function esc(t) { var e = document.createElement('div'); e.textContent = t; return e.innerHTML; }
function p2(n) { return (n < 10 ? '0' : '') + n; }
function drawCal() {
  var first = new Date(cy, cm, 1), days = new Date(cy, cm + 1, 0).getDate(), h = '';
  document.getElementById('cMonth').textContent = first.toLocaleDateString('en-IN', { month: 'long', year: 'numeric' });
  ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(function (w) { h += '<i>' + w + '</i>'; });
  for (var i = 0; i < first.getDay(); i++) h += '<span></span>';
  for (var d = 1; d <= days; d++) {
    var k = cy + '-' + p2(cm + 1) + '-' + p2(d), n = (CAL[k] || []).length;
    h += '<button class="d' + (k === TODAY ? ' today' : '') + (k < TODAY ? ' past' : '') + (k === sel ? ' on' : '') + '" data-d="' + k + '">' + d + (n ? '<em>' + n + '</em>' : '') + '</button>';
  }
  document.getElementById('calGrid').innerHTML = h;
  var list = CAL[sel] || [], dt = new Date(sel + 'T00:00:00');
  h = '<h4>' + dt.toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' }) + ' · ' + list.length + ' appointment' + (list.length === 1 ? '' : 's') + '</h4>';
  h += list.length ? '<ul>' + list.map(function (a) {
    return '<li><span class="tm">' + esc(a.t) + '</span><div class="who"><b>' + esc(a.n) + '</b><small>' + esc(a.s) + ' · ' + esc(a.b || '—') + '</small></div><span class="chip ' + a.st + '">' + a.st.charAt(0).toUpperCase() + a.st.slice(1) + '</span></li>';
  }).join('') + '</ul>' : '<div class="muted pad">No appointments on this day.</div>';
  document.getElementById('calDay').innerHTML = h;
}
function moveCal(s) { cm += s; if (cm < 0) { cm = 11; cy--; } else if (cm > 11) { cm = 0; cy++; } drawCal(); }
document.getElementById('cPrev').addEventListener('click', function () { moveCal(-1); });
document.getElementById('cNext').addEventListener('click', function () { moveCal(1); });
document.getElementById('calGrid').addEventListener('click', function (e) { var b = e.target.closest('.d'); if (b) { sel = b.dataset.d; drawCal(); } });
drawCal();

/* ---- table filter / search / show more ---- */
var PAGE = 10, shown = PAGE, filter = 'all', term = '';
var rows = Array.prototype.slice.call(document.querySelectorAll('#tbody tr.r'));
function apply() {
  var match = 0;
  rows.forEach(function (r) {
    var ok = (filter === 'all' || (filter === 'up' ? r.dataset.up === '1' : r.dataset.s === filter)) && (!term || r.dataset.q.indexOf(term) > -1);
    if (ok) { match++; r.style.display = match <= shown ? '' : 'none'; } else r.style.display = 'none';
  });
  document.getElementById('empty').style.display = match ? 'none' : 'block';
  var more = document.getElementById('more');
  more.style.display = match > shown ? 'block' : 'none';
  more.textContent = 'Show more (' + (match - shown) + ' left)';
}
function setFilter(f) {
  filter = f; shown = PAGE;
  document.querySelectorAll('#tabs .tab').forEach(function (x) { x.classList.toggle('on', x.dataset.f === f); });
  apply();
}
document.getElementById('tabs').addEventListener('click', function (e) { var b = e.target.closest('.tab'); if (b) setFilter(b.dataset.f); });
function setTerm(v, from) {
  term = v.trim().toLowerCase(); shown = PAGE;
  if (from !== 'q') document.getElementById('q').value = v;
  if (from !== 'gq') document.getElementById('gq').value = v;
  apply();
}
document.getElementById('q').addEventListener('input', function () { setTerm(this.value, 'q'); });
document.getElementById('gq').addEventListener('input', function () { setTerm(this.value, 'gq'); });
document.getElementById('gq').addEventListener('keydown', function (e) { if (e.key === 'Enter') location.hash = '#appts'; });
document.getElementById('more').addEventListener('click', function () { shown += PAGE; apply(); });
document.getElementById('bell').addEventListener('click', function () { setFilter('pending'); });
document.getElementById('heroPending').addEventListener('click', function () { setFilter('pending'); });
apply();

/* auto-hide flash message */
var fl = document.querySelector('.flash');
if (fl) setTimeout(function () { fl.style.transition = 'opacity .5s, transform .5s'; fl.style.opacity = 0; fl.style.transform = 'translateY(-6px)'; setTimeout(function () { fl.remove(); }, 500); }, 6000);
</script>
</body>
</html>
