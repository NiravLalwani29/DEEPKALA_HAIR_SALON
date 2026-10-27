<?php
require 'backend/config.php';
require 'backend/wallet.php';

$isLoggedIn = isset($_SESSION['user_id']);

$planJustActivated = false;
$refund = 0;
$membershipMessage = "";

if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['choose_plan'])) {
    $plan = mysqli_real_escape_string($conn, $_POST['choose_plan']);

    if ($plan == 'silver' || $plan == 'gold') {
        $uid = (int) $_SESSION['user_id'];
        mysqli_begin_transaction($conn);
        if ($plan == 'silver') {
            mysqli_query($conn, "UPDATE users SET membership = 'silver' WHERE id = $uid AND membership = 'gold'");
            if (mysqli_affected_rows($conn) > 0) {
                $refund = 999 - 499;
                wallet_credit($conn, $uid, $refund, null, 'Refund: switched from Gold to Silver membership');
            }
        }
        mysqli_query($conn, "UPDATE users SET membership = '$plan' WHERE id = $uid");
        mysqli_commit($conn);

        $_SESSION['membership'] = $plan;
        $planJustActivated = true;
    }
}

$currentPlan = $_SESSION['membership'] ?? 'none';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Membership — Deepkala Hair Salon, Ahmedabad</title>

  <link rel="icon" type="image/png" href="assets/deepkala-emblem.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:wght@400..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:wght@400..900&display=swap"></noscript>

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

    .member-status-bar {
      display: none;
      background-color: var(--card-cream);
      border: 1px solid var(--rose-accent);
      padding: 18px 22px;
      margin-bottom: 32px;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      flex-wrap: wrap;
    }

    .member-status-bar.show { display: flex; }

    .member-status-text { font-size: 0.9rem; color: #FFFFFF; }

    .member-status-text strong { color: var(--rose-accent); }

    .plans-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      margin-bottom: 60px;
    }
@media (min-width: 768px) {
 .plans-grid { grid-template-columns: 1fr 1fr; }
}

    .plan-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 36px 32px;
      display: flex;
      flex-direction: column;
      position: relative;
      transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .plan-card:hover { transform: translateY(-3px); }

    .plan-card.gold { border-color: var(--gold-glow); box-shadow: 0 10px 30px rgba(255, 214, 102, 0.08); }

    /* gold shimmer: sweeps across the Gold card every ~6s, one-time shine on member badge/button */
    .plan-card.gold { overflow: hidden; }
    .plan-card.gold::after, .btn-choose-plan.current::after, .nav-member-badge::after {
      content: ''; position: absolute; top: -20%; bottom: -20%; left: 0; width: 18%; pointer-events: none; opacity: 0;
      background: linear-gradient(90deg, transparent, rgba(255,214,102,0.34), transparent);
      transform: translateX(-180%) skewX(-12deg);
    }
    .plan-card.gold::after { animation: goldSweep 6s 0.6s ease-out infinite; }
    .btn-choose-plan.current, .nav-member-badge { position: relative; overflow: hidden; }
    .btn-choose-plan.current::after, .nav-member-badge::after { background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent); animation: goldSweep 1.1s 0.8s ease-out 1; }
    @keyframes goldSweep { 0% { opacity: 0; transform: translateX(-180%) skewX(-12deg); } 10% { opacity: 0.8; } 25%, 100% { opacity: 0; transform: translateX(620%) skewX(-12deg); } }
    @media (prefers-reduced-motion: reduce) { .plan-card.gold::after, .btn-choose-plan.current::after, .nav-member-badge::after { display: none; } }

    .plan-badge-corner {
      position: absolute; top: 0; right: 0;
      background-color: var(--gold-glow);
      color: var(--rose-deep);
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 6px 14px;
    }

    .plan-name { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }

    .plan-card.silver .plan-name { color: var(--rose-accent); }

    .plan-card.gold .plan-name { color: var(--gold-glow); }

    .plan-desc { font-size: 0.85rem; color: var(--text-3); margin-bottom: 22px; }

    .plan-price { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 2.4rem; font-weight: 700; color: var(--text-ink); }

    .plan-price span { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 0.9rem; font-weight: 500; color: var(--text-3); }

    .plan-discount-tag {
      display: inline-block;
      margin-top: 10px; margin-bottom: 22px;
      font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
      padding: 5px 12px; border-radius: 4px;
    }

    .plan-card.silver .plan-discount-tag { background-color: rgba(201,166,107,0.15); color: var(--rose-accent); border: 1px solid rgba(201,166,107,0.4); }

    .plan-card.gold .plan-discount-tag { background-color: rgba(255,214,102,0.15); color: var(--gold-glow); border: 1px solid rgba(255,214,102,0.45); }

    .plan-features { list-style: none; margin-bottom: 28px; flex: 1; }

    .plan-features li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; color: #C9C2B4; padding: 8px 0; border-bottom: 1px solid #2A2419; }

    .plan-features li:last-child { border-bottom: none; }

    .plan-features li svg { flex-shrink: 0; margin-top: 3px; }

    .plan-card.silver .plan-features li svg { color: var(--rose-accent); }

    .plan-card.gold .plan-features li svg { color: var(--gold-glow); }

    .btn-choose-plan {
      display: inline-flex; align-items: center; justify-content: center; gap: 10px;
      padding: 15px 24px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em;
      cursor: pointer; border-radius: 4px; transition: all 0.2s ease; width: 100%; min-height: 46px;
    }

    .plan-card.silver .btn-choose-plan { background-color: var(--rose-accent); color: #FFF; border: 1px solid var(--rose-accent); }

    .plan-card.silver .btn-choose-plan:hover { background-color: var(--rose-deep); border-color: var(--rose-deep); }

    .plan-card.gold .btn-choose-plan { background-color: var(--gold-glow); color: var(--rose-deep); border: 1px solid var(--gold-glow); }

    .plan-card.gold .btn-choose-plan:hover { background-color: #FFE28C; }

    .btn-choose-plan.current { background-color: var(--card-cream) !important; color: var(--text-3) !important; border: 1px solid var(--border-hairline) !important; cursor: default; }

    .modal-overlay { display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 24px; }

    .modal-content { background-color: var(--bg-cream); border: 1px solid var(--border-hairline); border-radius: 8px; padding: 36px; max-width: 460px; width: 100%; text-align: left; box-shadow: 0 20px 48px rgba(0,0,0,0.5); }

    .modal-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.4rem; font-weight: 700; color: var(--text-ink); margin-bottom: 6px; text-align: center; }

    .modal-sub { font-size: 0.82rem; color: var(--text-2); text-align: center; margin-bottom: 22px; }

    .payment-amount-bar { display: flex; align-items: center; justify-content: space-between; background-color: var(--card-cream); border: 1px solid var(--border-hairline); padding: 14px 18px; margin-bottom: 20px; flex-wrap: wrap; gap: 8px; }

    .payment-amount-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-2); }

    .payment-amount-value { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.35rem; font-weight: 700; color: var(--rose-accent); }

    .pay-method-toggle { display: flex; gap: 10px; margin-bottom: 20px; }

    .pay-method-pill { flex: 1; text-align: center; padding: 12px 10px; border: 1px solid var(--border-hairline); background-color: var(--card-white); font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; user-select: none; }

    .pay-method-pill.selected { background-color: var(--rose-deep); border-color: var(--rose-deep); color: #FFF; }

    .pay-fields { display: none; flex-direction: column; gap: 14px; }

    .pay-fields.active { display: flex; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .form-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-ink); }

    .form-input { padding: 11px 14px; border: 1px solid var(--border-hairline); background-color: var(--card-white); font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 15px; color: var(--text-ink); width: 100%; min-height: 42px; border-radius: 4px; }

    .form-input:focus { outline: 2px solid var(--rose-accent); border-color: var(--rose-accent); }

    .field-error { font-size: 0.72rem; color: #FF6B60; display: none; }

    .field-error.show { display: block; }

    .form-input.invalid { border-color: #FF6B60; }

    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .btn-submit { display: inline-flex; align-items: center; justify-content: center; gap: 10px; background-color: var(--rose-accent); color: var(--on-primary); padding: 15px 24px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; border: 1px solid var(--rose-accent); cursor: pointer; width: 100%; border-radius: 4px; margin-top: 20px; min-height: 46px; }

    .btn-submit:hover { background-color: var(--rose-accent-hover); border-color: var(--rose-accent-hover); }

    .btn-link-muted { background: none; border: none; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--bronze-taupe); cursor: pointer; padding: 8px; width: 100%; margin-top: 6px; }

    .processing-box { padding: 10px 0; text-align: center; }

    .spinner { width: 40px; height: 40px; border: 3px solid var(--border-hairline); border-top-color: var(--rose-deep); border-radius: 50%; margin: 0 auto 16px auto; animation: spin 0.8s linear infinite; }

    @keyframes spin { to { transform: rotate(360deg); } }

    .success-box { text-align: center; }

    .success-check { width: 56px; height: 56px; border-radius: 50%; background-color: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.4); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; }

    .success-summary { background-color: var(--card-cream); border: 1px solid var(--border-hairline); padding: 14px 18px; margin: 18px 0; font-size: 0.85rem; color: #C9C2B4; text-align: left; border-radius: 4px; }

    .success-summary strong { color: var(--text-ink); }

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

      .field-row { grid-template-columns: 1fr; }
}

    .dk-member-badge { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; padding: 3px 9px; border-radius: 4px; white-space: nowrap; }

    .dk-badge-silver { background-color: rgba(201,166,107,0.15); color: #C9A66B; border: 1px solid rgba(201,166,107,0.4); }

    .dk-badge-gold { background-color: rgba(255,214,102,0.15); color: var(--highlight); border: 1px solid rgba(255,214,102,0.45); }

    .nav-member-badge { margin-left: 8px; font-size: 0.55rem !important; padding: 2px 7px !important; vertical-align: middle; }
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
          <li><a href="membership.php" class="nav-link active" aria-current="page"><span class="nav-underline" aria-hidden="true"></span>Membership<span id="navMemberBadge"><?php if ($currentPlan !== 'none') { ?><span class="dk-member-badge dk-badge-<?php echo $currentPlan; ?> nav-member-badge"><?php echo $currentPlan === 'gold' ? 'Gold' : 'Silver'; ?></span><?php } ?></span></a></li>
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
<h1 class="font-serif">Deepkala Membership</h1>
      </div>
    </section>

    <div class="container" style="padding-bottom: 40px;">

      <div class="category-header reveal">
        <span class="eyebrow-tag">CHOOSE YOUR PLAN</span>
        <div class="hairline"></div>
      </div>

      <?php if ($currentPlan !== 'none') { ?>
        <div class="member-status-bar reveal show" id="memberStatusBar">
          <div class="member-status-text" id="memberStatusText">You're already a <strong><?php echo $currentPlan === 'gold' ? 'Gold Member' : 'Silver Member'; ?></strong> — enjoying <?php echo $currentPlan === 'gold' ? 20 : 10; ?>% off every service.</div>
        </div>
      <?php } ?>

      <div class="plans-grid">

        <div class="plan-card silver reveal">
          <div class="plan-name">Silver</div>
          <div class="plan-desc">A simple way to save on every regular visit.</div>
          <div class="plan-price">₹499 <span>/ year</span></div>
          <div class="plan-discount-tag">10% off all services</div>
          <ul class="plan-features">
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> 10% off every service, automatically applied</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Valid for 12 months from purchase</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Priority WhatsApp booking confirmation</li>
          </ul>
          <?php if ($currentPlan === 'silver') { ?>
            <button type="button" class="btn-choose-plan current" id="btnPlanSilver" disabled>Current Plan</button>
          <?php } else { ?>
            <button type="button" class="btn-choose-plan" onclick="openPlanCheckout('silver')" id="btnPlanSilver"><?php echo $currentPlan === 'gold' ? 'Switch to Silver' : 'Choose Silver'; ?></button>
          <?php } ?>
        </div>

        <div class="plan-card gold reveal">
          <div class="plan-badge-corner">Most Popular</div>
          <div class="plan-name">Gold</div>
          <div class="plan-desc">For regulars who want the best pricing on everything.</div>
          <div class="plan-price">₹999 <span>/ year</span></div>
          <div class="plan-discount-tag">20% off all services</div>
          <ul class="plan-features">
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> 20% off every service, automatically applied</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Valid for 12 months from purchase</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Priority WhatsApp booking confirmation</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Complimentary head massage on bridal &amp; party packages</li>
          </ul>
          <?php if ($currentPlan === 'gold') { ?>
            <button type="button" class="btn-choose-plan current" id="btnPlanGold" disabled>Current Plan</button>
          <?php } else { ?>
            <button type="button" class="btn-choose-plan" onclick="openPlanCheckout('gold')" id="btnPlanGold"><?php echo $currentPlan === 'silver' ? 'Switch to Gold' : 'Choose Gold'; ?></button>
          <?php } ?>
        </div>

      </div>

    </div>
  </main>

  <div class="modal-overlay" id="checkoutModal">
    <div class="modal-content">

      <form id="membershipForm" method="POST" action="membership.php">
      <input type="hidden" name="choose_plan" id="hiddenChoosePlan" value="">
      <div id="modalPaymentForm">
        <h3 class="modal-title" id="checkoutPlanTitle">Silver Membership</h3>
        <p class="modal-sub">Complete payment to activate your membership.</p>

        <div class="payment-amount-bar">
          <span class="payment-amount-label" id="checkoutAmountLabel">Amount Payable</span>
          <span class="payment-amount-value" id="checkoutAmount">₹499</span>
        </div>
        <p id="switchNote" style="display:none;font-size:0.82rem;color:var(--bronze-taupe);margin:-10px 0 16px;"></p>

        <div class="pay-method-toggle" role="tablist" id="payMethodToggle">
          <div class="pay-method-pill selected" id="payTabCard" onclick="selectPayMethod('card')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-3px;margin-right:4px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>Card</div>
          <div class="pay-method-pill" id="payTabUpi" onclick="selectPayMethod('upi')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-3px;margin-right:4px;"><rect x="7" y="2" width="10" height="20" rx="2"/><line x1="11" y1="18" x2="13" y2="18"/></svg>UPI</div>
        </div>

        <div class="pay-fields active" id="cardFields">
          <div class="form-group">
            <label class="form-label" for="cardName">Name on Card *</label>
            <input type="text" id="cardName" name="cc-name" class="form-input" placeholder="Ramesh Patel" autocomplete="cc-name" aria-describedby="err-cardName">
            <span class="field-error" id="err-cardName">Enter the name on the card.</span>
          </div>
          <div class="form-group">
            <label class="form-label" for="cardNumber">Card Number *</label>
            <input type="text" id="cardNumber" name="cc-number" class="form-input" placeholder="1234 5678 9012 3456" inputmode="numeric" maxlength="19" autocomplete="cc-number" aria-describedby="err-cardNumber">
            <span class="field-error" id="err-cardNumber">Enter a valid 16-digit card number.</span>
          </div>
          <div class="field-row">
            <div class="form-group">
              <label class="form-label" for="cardExpiry">Expiry (MM/YY) *</label>
              <input type="text" id="cardExpiry" name="cc-exp" class="form-input" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp" aria-describedby="err-cardExpiry">
              <span class="field-error" id="err-cardExpiry">Enter a valid future date.</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="cardCvv">CVV *</label>
              <input type="password" id="cardCvv" name="cc-csc" class="form-input" placeholder="•••" inputmode="numeric" maxlength="4" autocomplete="cc-csc" aria-describedby="err-cardCvv">
              <span class="field-error" id="err-cardCvv">Enter a valid CVV.</span>
            </div>
          </div>
        </div>

        <div class="pay-fields" id="upiFields">
          <div class="form-group">
            <label class="form-label" for="upiId">UPI ID *</label>
            <input type="text" id="upiId" name="upiId" class="form-input" placeholder="yourname@upi" aria-describedby="err-upiId">
            <span class="field-error" id="err-upiId">Enter a valid UPI ID (e.g. name@bank).</span>
          </div>
        </div>

        <button type="button" class="btn-submit" onclick="submitMembershipPayment()">Pay &amp; Activate Membership</button>
        <button type="button" class="btn-link-muted" onclick="closeCheckoutModal()">Cancel</button>
      </div>
      </form>

      <div id="modalProcessing" style="display:none;">
        <div class="processing-box">
          <div class="spinner"></div>
          <h3 class="modal-title">Processing Payment…</h3>
          <p class="modal-sub" style="margin-bottom:0;">Please wait while we (simulate) confirming your payment.</p>
        </div>
      </div>

      <div id="modalSuccess" style="display:none;">
        <div class="success-box">
          <div class="success-check">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#3FDB74" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <h3 class="modal-title" id="successTitle">Welcome to the Family!</h3>
          <p class="modal-sub" id="successSub">Your membership is now active.</p>

          <div class="success-summary" id="successSummary"></div>

          <button type="button" class="btn-submit" onclick="window.location.href='services.php'">Browse Services at Member Pricing</button>
          <button type="button" class="btn-link-muted" onclick="closeCheckoutModal()">Close</button>
        </div>
      </div>

    </div>
  </div>

<?php include 'backend/footer.php'; ?>

  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
      }, { threshold: 0.1 });
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
        menuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleMenu(); });
        document.addEventListener('click', (e) => { if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) toggleMenu(false); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleMenu(false); });
        navLinks.querySelectorAll('a').forEach(link => link.addEventListener('click', () => toggleMenu(false)));
      }

      const header = document.getElementById('siteHeader');
      if (header) {
        window.addEventListener('scroll', () => {
          header.style.boxShadow = window.scrollY > 20 ? '0 4px 12px rgba(0,0,0,0.35)' : 'none';
        }, { passive: true });
      }

      const cardNumberEl = document.getElementById('cardNumber');
      if (cardNumberEl) cardNumberEl.addEventListener('input', () => {
        let digits = cardNumberEl.value.replace(/\D/g, '').slice(0, 16);
        cardNumberEl.value = digits.replace(/(.{4})/g, '$1 ').trim();
      });
      const cardExpiryEl = document.getElementById('cardExpiry');
      if (cardExpiryEl) cardExpiryEl.addEventListener('input', () => {
        let digits = cardExpiryEl.value.replace(/\D/g, '').slice(0, 4);
        cardExpiryEl.value = digits.length >= 3 ? digits.slice(0,2) + '/' + digits.slice(2) : digits;
      });
      const cardCvvEl = document.getElementById('cardCvv');
      if (cardCvvEl) cardCvvEl.addEventListener('input', () => {
        cardCvvEl.value = cardCvvEl.value.replace(/\D/g, '').slice(0, 4);
      });

    });

    const PLANS = {
      silver: { label: 'Silver Membership', price: 499, discount: 10 },
      gold:   { label: 'Gold Membership',   price: 999, discount: 20 }
    };

    var userIsLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    var userCurrentPlan = "<?php echo $currentPlan; ?>";

    let currentPlanKey = null;
    let currentPayMethod = 'card';
    let currentSwitchAmount = 0;

    function openPlanCheckout(planKey) {
      if (userCurrentPlan === planKey) return;

      if (!userIsLoggedIn) {
        window.location.href = 'login.php?redirect=membership.php';
        return;
      }

      currentPlanKey = planKey;
      const plan = PLANS[planKey];
      document.getElementById('checkoutPlanTitle').textContent = plan.label;

      const note = document.getElementById('switchNote');
      const label = document.getElementById('checkoutAmountLabel');
      const amountEl = document.getElementById('checkoutAmount');
      const toggle = document.getElementById('payMethodToggle');

      if (userCurrentPlan !== 'none') {
        const oldPrice = PLANS[userCurrentPlan].price;
        const diff = plan.price - oldPrice;

        if (diff > 0) {
          currentSwitchAmount = diff;
          label.textContent = 'Upgrade Amount Payable';
          amountEl.textContent = '₹' + diff.toLocaleString('en-IN');
          note.style.display = 'block';
          note.textContent = `Your existing ₹${oldPrice} ${userCurrentPlan} membership is adjusted — you only pay the ₹${diff} difference to upgrade to ${plan.label}.`;
          toggle.style.display = '';
          document.getElementById('modalPaymentForm').style.display = 'block';
        } else {
          currentSwitchAmount = 0;
          label.textContent = 'Amount Payable';
          amountEl.textContent = '₹0';
          note.style.display = 'block';
          note.textContent = `You're switching down from ${userCurrentPlan} to ${plan.label}. The ₹${Math.abs(diff)} difference will be refunded to your Deepkala wallet instantly. No extra payment needed — just confirm below.`;
          toggle.style.display = 'none';
          document.getElementById('cardFields').classList.remove('active');
          document.getElementById('upiFields').classList.remove('active');
        }
      } else {
        currentSwitchAmount = plan.price;
        label.textContent = 'Amount Payable';
        amountEl.textContent = '₹' + plan.price.toLocaleString('en-IN');
        note.style.display = 'none';
        toggle.style.display = '';
      }

      document.getElementById('modalPaymentForm').style.display = 'block';
      document.getElementById('modalProcessing').style.display = 'none';
      document.getElementById('modalSuccess').style.display = 'none';
      document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
      document.getElementById('checkoutModal').style.display = 'none';
    }

    function selectPayMethod(method) {
      currentPayMethod = method;
      document.getElementById('payTabCard').classList.toggle('selected', method === 'card');
      document.getElementById('payTabUpi').classList.toggle('selected', method === 'upi');
      document.getElementById('cardFields').classList.toggle('active', method === 'card');
      document.getElementById('upiFields').classList.toggle('active', method === 'upi');
      document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
      document.querySelectorAll('.form-input.invalid').forEach(el => el.classList.remove('invalid'));
    }

    function showFieldError(id, show) {
      const err = document.getElementById('err-' + id);
      const input = document.getElementById(id);
      if (err) err.classList.toggle('show', show);
      if (input) {
        input.classList.toggle('invalid', show);
        input.setAttribute('aria-invalid', show ? 'true' : 'false');
      }
    }

    function validatePayment() {
      let valid = true;
      if (currentPayMethod === 'card') {
        const name = document.getElementById('cardName').value.trim();
        const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiry = document.getElementById('cardExpiry').value.trim();
        const cvv = document.getElementById('cardCvv').value.trim();

        const nameOk = name.length >= 2;
        showFieldError('cardName', !nameOk); if (!nameOk) valid = false;

        const numberOk = /^\d{16}$/.test(number);
        showFieldError('cardNumber', !numberOk); if (!numberOk) valid = false;

        let expiryOk = /^\d{2}\/\d{2}$/.test(expiry);
        if (expiryOk) {
          const [mm, yy] = expiry.split('/').map(Number);
          const now = new Date();
          const curYY = now.getFullYear() % 100;
          const curMM = now.getMonth() + 1;
          if (mm < 1 || mm > 12) expiryOk = false;
          else if (yy < curYY || (yy === curYY && mm < curMM)) expiryOk = false;
        }
        showFieldError('cardExpiry', !expiryOk); if (!expiryOk) valid = false;

        const cvvOk = /^\d{3,4}$/.test(cvv);
        showFieldError('cardCvv', !cvvOk); if (!cvvOk) valid = false;
      } else {
        const upi = document.getElementById('upiId').value.trim();
        const upiOk = /^[\w.\-]{2,}@[a-zA-Z]{2,}$/.test(upi);
        showFieldError('upiId', !upiOk); if (!upiOk) valid = false;
      }
      return valid;
    }

    function maskPaymentMethod() {
      if (currentPayMethod === 'card') {
        const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
        return `Card •••• ${number.slice(-4)}`;
      } else {
        const upi = document.getElementById('upiId').value.trim();
        return `UPI (${upi})`;
      }
    }

    function submitMembershipPayment() {
      if (currentSwitchAmount > 0 && !validatePayment()) return;

      document.getElementById('modalPaymentForm').style.display = 'none';
      document.getElementById('modalProcessing').style.display = 'block';

      document.getElementById('hiddenChoosePlan').value = currentPlanKey;
      document.getElementById('membershipForm').submit();
    }

    var planJustActivated = <?php echo $planJustActivated ? 'true' : 'false'; ?>;
    <?php if ($planJustActivated) { ?>
    window.addEventListener('DOMContentLoaded', function () {
      document.getElementById('successTitle').textContent = <?php echo json_encode($currentPlan === 'gold' ? 'Welcome to Gold!' : 'Welcome to Silver!'); ?>;
      document.getElementById('successSub').textContent = <?php echo json_encode($refund ? "₹$refund has been refunded to your Deepkala wallet." : 'Your membership is now active.'); ?>;
      document.getElementById('successSummary').innerHTML =
        '<strong><?php echo $currentPlan === 'gold' ? 'Gold Membership' : 'Silver Membership'; ?></strong> activated<br><?php echo $currentPlan === 'gold' ? 20 : 10; ?>% off every service, automatically';

      document.getElementById('modalPaymentForm').style.display = 'none';
      document.getElementById('modalProcessing').style.display = 'none';
      document.getElementById('modalSuccess').style.display = 'block';
      document.getElementById('checkoutModal').style.display = 'flex';
    });
    <?php } ?>
  </script>
</body>
</html>
