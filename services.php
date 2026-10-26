<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services & Pricing — Deepkala Hair Salon, Ahmedabad</title>

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
      text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
      overflow-x: hidden;
      width: 100%;
    }

    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      width: 100%;
      overflow-x: hidden;
    }

    img, svg, iframe, video {
      max-width: 100%;
      height: auto;
    }

    h1, h2, h3, h4, h5, h6, p, span, a, label, button, input, select, textarea {
      overflow-wrap: break-word;
      word-break: break-word;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px;
    }
@media (max-width: 768px) {
      .container {
        padding: 0 16px;
      }
}
@media (max-width: 380px) {
      .container {
        padding: 0 12px;
      }
}

    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; }

    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }

    .eyebrow-tag {
      display: inline-block;
      background-color: var(--brand);
      color: var(--on-brand);
      padding: 6px 14px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    .category-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-top: 48px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1;
      height: 1px;
      background-color: var(--border-hairline);
    }

    .services-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 16px;
    }
@media (min-width: 768px) {
      .services-grid {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
      }
}

    .service-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 24px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
    }

    .service-card:focus-visible {
      outline: 2px solid var(--rose-accent, #C9A66B);
      outline-offset: 3px;
    }

    .service-card:hover {
      transform: translateY(-2px);
      border-color: var(--bronze-taupe);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
    }

    .service-card {
      position: relative;
      overflow: hidden;
      isolation: isolate;
      border-color: rgba(232, 199, 132, 0.35) !important;
    }

    .service-card::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('assets/bg-cc5e5628.jpg');
      background-size: cover;
      background-position: center;
      z-index: -2;
    }

    .service-card::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(201,166,107,0.35) 0%, rgba(10,8,6,0.72) 100%);
      z-index: -1;
    }

    .service-card > * {
      position: relative;
      z-index: 1;
    }

    .service-card .service-title,
    .service-card .service-price {
      color: #FFFFFF !important;
    }

    .service-card .service-desc {
      color: #FFFFFF !important;
    }

    .service-card .book-mini-btn {
      color: #E8C784 !important;
    }

    .service-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 4px;
    }

    .service-desc {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 0.72rem;
      font-weight: 500;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .service-price {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-ink);
      white-space: nowrap;
    }

    .book-mini-btn {
      margin-top: 8px;
      display: inline-block;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--rose-accent);
    }

    .site-header {
      position: sticky;
      top: 0;
      z-index: 100;
      background-color: rgba(21, 19, 14, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--border-hairline);
      transition: padding 0.3s ease, box-shadow 0.3s ease;
    }

    .header-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 76px;
    }

    .brand-logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-text-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0;
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      color: var(--text-ink);
      line-height: 1.1;
    }

    .logo-text-sub {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--bronze-taupe);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 28px;
      list-style: none;
    }

    .nav-link {
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-ink);
      padding: 6px 0;
      position: relative;
      transition: color 0.2s ease;
    }

    .nav-link:hover, .nav-link.active {
      color: var(--rose-accent);
    }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: var(--rose-accent);
    }

    .header-book-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 9px 20px;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      background-color: var(--rose-accent);
      color: var(--on-primary);
      border: 1px solid var(--rose-accent);
      border-radius: 4px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .header-book-btn:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      color: var(--on-primary);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(10, 8, 6, 0.35);
    }

    .btn-whatsapp-cta {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 10px 16px;
      background-color: var(--whatsapp-green);
      color: var(--on-whatsapp) !important;
      font-size: 0.85rem;
      font-weight: 600;
      border-radius: 4px;
      transition: background-color 0.2s ease;
      box-shadow: 0 2px 8px rgba(37, 211, 102, 0.25);
    }

    .btn-whatsapp-cta:hover {
      background-color: var(--whatsapp-hover);
      color: var(--on-whatsapp) !important;
    }

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
      color: var(--text-ink);
    }

    .page-hero {
      position: relative;
      padding: 60px 0 40px 0;
      text-align: center;
      border-bottom: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      overflow: hidden;
      isolation: isolate;
    }

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

    .page-hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(10,8,6,0.82) 0%, rgba(201,166,107,0.38) 55%, rgba(10,8,6,0.88) 100%);
      z-index: -1;
    }

    .page-hero > .container {
      position: relative;
      z-index: 1;
    }

    .page-hero h1 {
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 12px;
    }

    .page-hero p {
      font-size: 1rem;
      color: var(--text-2);
      max-width: 600px;
      margin: 0 auto;
    }

    .filter-bar {
      position: sticky;
      top: 76px;
      z-index: 90;
      background-color: rgba(21, 19, 14, 0.97);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border-hairline);
      padding: 18px 0;
    }

    .filter-bar-inner {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .filter-search {
      position: relative;
      width: 100%;
    }

    .filter-search svg {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      color: var(--bronze-taupe);
      pointer-events: none;
    }

    .filter-search input {
      width: 100%;
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      color: var(--text-ink);
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 0.9rem;
      padding: 11px 14px 11px 38px;
      border-radius: 999px;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .filter-search input::placeholder {
      color: var(--text-3);
    }

    .filter-search input:focus {
      border-color: var(--rose-accent);
      box-shadow: 0 0 0 3px rgba(201, 166, 107, 0.15);
    }

    .filter-search .clear-search {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 22px;
      height: 22px;
      display: none;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: transparent;
      border: none;
      color: #9A907C;
      cursor: pointer;
      transition: color 0.2s ease, background-color 0.2s ease;
    }

    .filter-search .clear-search:hover {
      color: var(--text-ink);
      background-color: rgba(255, 255, 255, 0.08);
    }

    .filter-search.has-value .clear-search {
      display: flex;
    }

    .filter-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: flex-start;
    }

    .filter-pill {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-ink);
      background-color: transparent;
      border: 1px solid var(--border-hairline);
      padding: 9px 16px;
      border-radius: 999px;
      cursor: pointer;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .filter-pill:hover {
      border-color: var(--bronze-taupe);
      color: var(--rose-accent);
    }

    .filter-pill.active {
      background-color: var(--rose-accent);
      border-color: var(--rose-accent);
      color: var(--rose-deep);
    }

    .pill-count {
      opacity: 0.6;
      margin-left: 5px;
      font-weight: 500;
    }

    .filter-pill.active .pill-count {
      opacity: 0.75;
    }

    .filter-count {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 0.72rem;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #8A8270;
      margin-top: 10px;
      text-align: right;
    }
@media (max-width: 768px) {
      .filter-bar {
        top: 76px;
      }

      .filter-pills {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 2px;
        margin: -2px -2px 0;
      }

      .filter-pills::-webkit-scrollbar {
        display: none;
      }

      .filter-count {
        text-align: left;
      }
}

    .no-results {
      display: none;
      text-align: center;
      padding: 80px 20px;
      border: 1px dashed var(--border-hairline);
      margin-top: 32px;
    }

    .no-results.show {
      display: block;
    }

    .no-results .no-results-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.4rem;
      margin-bottom: 8px;
      color: var(--text-ink);
    }

    .no-results p {
      color: #9A907C;
      font-size: 0.9rem;
      margin-bottom: 20px;
    }

    .no-results button {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--rose-deep);
      background-color: var(--rose-accent);
      border: none;
      padding: 12px 24px;
      border-radius: 999px;
      cursor: pointer;
    }

    .service-card.hidden,
    section[data-category].hidden {
      display: none !important;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: var(--rose-accent);
      color: var(--on-primary);
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--rose-accent);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      transform: translateY(-2px);
    }

    main { flex: 1 0 auto; }

    .whatsapp-float {
      position: fixed;
      bottom: max(20px, env(safe-area-inset-bottom, 20px));
      right: max(20px, env(safe-area-inset-right, 20px));
      width: 52px;
      height: 52px;
      min-width: 48px;
      min-height: 48px;
      background-color: var(--whatsapp-green);
      color: var(--on-whatsapp);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
      z-index: 999;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .whatsapp-float:hover {
      transform: scale(1.08) translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
    }

    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    .mobile-menu-cta {
      display: none;
    }
@media (max-width: 860px) {
      .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: var(--bg-cream);
        border-bottom: 2px solid var(--rose-accent);
        flex-direction: column;
        padding: 16px 20px 24px 20px;
        gap: 8px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45);
        z-index: 999;
        max-height: calc(100vh - 80px);
        overflow-y: auto;
      }

      .nav-links.show {
        display: flex;
      }

      .nav-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        font-size: 0.9rem;
        width: 100%;
        border-bottom: 1px solid var(--border-hairline);
        border-radius: 4px;
      }

      .nav-link:hover, .nav-link.active {
        background-color: var(--card-cream);
      }

      .mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        min-height: 44px;
        border: 1px solid var(--border-hairline);
        border-radius: 4px;
      }

      .mobile-menu-cta {
        display: block;
        margin-top: 8px;
      }
}

    .dk-price-old {
      text-decoration: line-through;
      color: var(--text-3);
      font-size: 0.78em;
      font-weight: 500;
      margin-right: 2px;
    }

    .dk-price-new {
      color: var(--rose-accent);
      font-weight: 700;
    }

    .dk-member-priced {
      display: inline-flex;
      align-items: baseline;
      gap: 7px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .dk-member-badge {
      font-size: 0.6rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 3px 9px;
      border-radius: 4px;
      white-space: nowrap;
    }

    .dk-badge-silver {
      background-color: rgba(201, 166, 107, 0.15);
      color: #C9A66B;
      border: 1px solid rgba(201, 166, 107, 0.4);
    }

    .dk-badge-gold {
      background-color: rgba(255, 214, 102, 0.15);
      color: var(--highlight);
      border: 1px solid rgba(255, 214, 102, 0.45);
    }

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
          <li><a href="services.php" class="nav-link active" aria-current="page"><span class="nav-underline" aria-hidden="true"></span>Services</a></li>
          <li><a href="reviews.php" class="nav-link">Reviews</a></li>
          <li><a href="about.php" class="nav-link">About</a></li>
          <li><a href="contact.php" class="nav-link">Contact</a></li>
          <li><a href="membership.php" class="nav-link">Membership<span id="navMemberBadge"><?php $dkTier = $_SESSION['membership'] ?? 'none'; if ($dkTier === 'gold' || $dkTier === 'silver') { echo '<span class="dk-member-badge dk-badge-' . $dkTier . ' nav-member-badge">' . ucfirst($dkTier) . '</span>'; } ?></span></a></li>
          <li class="mobile-menu-cta">
            <a href="https://wa.me/919924620958?text=Hello%20Deepkala%20Hair%20Salon%2C%20I%20would%20like%20to%20book%20an%20appointment" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-cta">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.893 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
              Chat on WhatsApp
            </a>
          </li>
        </ul>
      </nav>

      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="booking.php" class="header-book-btn" title="Book Appointment at Deepkala Hair Salon">
          Book Appointment
        </a>
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
<h1 class="font-serif">Services & Pricing</h1>
      </div>
    </section>

    <div class="filter-bar">
      <div class="container">
        <div class="filter-bar-inner">
          <div class="filter-search" id="filterSearchWrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="serviceSearch" placeholder="Search a service…" aria-label="Search services">
            <button type="button" class="clear-search" id="clearSearch" aria-label="Clear search">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
          </div>
          <div class="filter-pills" id="filterPills" role="group" aria-label="Filter services by category">
            <button type="button" class="filter-pill active" data-category="all" aria-pressed="true">All Services<span class="pill-count"></span></button>
            <button type="button" class="filter-pill" data-category="cut-style" aria-pressed="false">Cut &amp; Style<span class="pill-count"></span></button>
            <button type="button" class="filter-pill" data-category="coloring" aria-pressed="false">Coloring<span class="pill-count"></span></button>
            <button type="button" class="filter-pill" data-category="spa-treatment" aria-pressed="false">Spa &amp; Treatment<span class="pill-count"></span></button>
            <button type="button" class="filter-pill" data-category="beard" aria-pressed="false">Beard<span class="pill-count"></span></button>
            <button type="button" class="filter-pill" data-category="bridal-party" aria-pressed="false">Bridal / Party<span class="pill-count"></span></button>
          </div>
        </div>
        <div class="filter-count" id="filterCount" role="status" aria-live="polite" aria-atomic="true"></div>
      </div>
    </div>

    <div class="container" style="padding-bottom: 60px;">

      <div class="no-results" id="noResults">
        <div class="no-results-title">No services found</div>
        <p>Try a different search term or choose another category.</p>
        <button type="button" id="resetFilters">Clear Filters</button>
      </div>

      <section class="reveal" data-category="cut-style">
        <div class="category-header">
          <h2 class="eyebrow-tag">CUT &amp; STYLE</h2>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <a class="service-card" href="booking.php?service=Buzz%20Cut">
            <div>
              <h3 class="service-title">Buzz Cut</h3>
              <p class="service-desc">EXTRA SHORT &amp; EASY TO MAINTAIN</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹200</span>
          </a>

          <a class="service-card" href="booking.php?service=Crew%20Cut">
            <div>
              <h3 class="service-title">Crew Cut</h3>
              <p class="service-desc">CLEAN, NEAT &amp; CLASSIC STYLE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹250</span>
          </a>

          <a class="service-card" href="booking.php?service=Fade%20Cut">
            <div>
              <h3 class="service-title">Fade Cut</h3>
              <p class="service-desc">SMOOTH FADE WITH MODERN FINISH</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹350</span>
          </a>

          <a class="service-card" href="booking.php?service=Undercut">
            <div>
              <h3 class="service-title">Undercut</h3>
              <p class="service-desc">SHORT SIDES WITH LONGER TOP</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹400</span>
          </a>

          <a class="service-card" href="booking.php?service=Pompadour">
            <div>
              <h3 class="service-title">Pompadour</h3>
              <p class="service-desc">VOLUMINOUS &amp; STYLISH LOOK</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹500</span>
          </a>

          <a class="service-card" href="booking.php?service=Quiff">
            <div>
              <h3 class="service-title">Quiff</h3>
              <p class="service-desc">TEXTURED FRONT WITH NATURAL VOLUME</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹450</span>
          </a>

          <a class="service-card" href="booking.php?service=Side%20Part">
            <div>
              <h3 class="service-title">Side Part</h3>
              <p class="service-desc">TIMELESS &amp; PROFESSIONAL STYLE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹300</span>
          </a>

          <a class="service-card" href="booking.php?service=French%20Crop">
            <div>
              <h3 class="service-title">French Crop</h3>
              <p class="service-desc">SHORT, TEXTURED &amp; TRENDY</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹350</span>
          </a>

          <a class="service-card" href="booking.php?service=Textured%20Crop">
            <div>
              <h3 class="service-title">Textured Crop</h3>
              <p class="service-desc">ADDS TEXTURE &amp; VOLUME</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹450</span>
          </a>

          <a class="service-card" href="booking.php?service=Caesar%20Cut">
            <div>
              <h3 class="service-title">Caesar Cut</h3>
              <p class="service-desc">SHORT CUT WITH STRAIGHT FRINGE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹300</span>
          </a>

          <a class="service-card" href="booking.php?service=Ivy%20League%20Cut">
            <div>
              <h3 class="service-title">Ivy League Cut</h3>
              <p class="service-desc">CLASSIC PRESERVED LENGTH ON TOP</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹400</span>
          </a>

          <a class="service-card" href="booking.php?service=Comb%20Over">
            <div>
              <h3 class="service-title">Comb Over</h3>
              <p class="service-desc">SLICKED SIDE-SWEPT FINISH</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹350</span>
          </a>
        </div>
      </section>

      <section class="reveal" data-category="coloring">
        <div class="category-header">
          <h2 class="eyebrow-tag">COLORING</h2>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <a class="service-card" href="booking.php?service=Root%20Touch-Up">
            <div>
              <h3 class="service-title">Root Touch-Up</h3>
              <p class="service-desc">SEAMLESS ROOT COLOR REFRESH</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1300</span>
          </a>

          <a class="service-card" href="booking.php?service=Highlights">
            <div>
              <h3 class="service-title">Highlights</h3>
              <p class="service-desc">SOFT STREAKS FOR ADDED DIMENSION</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1700</span>
          </a>

          <a class="service-card" href="booking.php?service=Lowlights">
            <div>
              <h3 class="service-title">Lowlights</h3>
              <p class="service-desc">ADDS DEPTH &amp; NATURAL CONTRAST</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1500</span>
          </a>

          <a class="service-card" href="booking.php?service=Gloss%20Treatment">
            <div>
              <h3 class="service-title">Gloss Treatment</h3>
              <p class="service-desc">BOOSTS SHINE &amp; COLOR VIBRANCY</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹2000</span>
          </a>

          <a class="service-card" href="booking.php?service=Color%20Correction">
            <div>
              <h3 class="service-title">Color Correction</h3>
              <p class="service-desc">FIXES UNEVEN OR FADED COLOR</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1600</span>
          </a>

          <a class="service-card" href="booking.php?service=Fashion%20Color">
            <div>
              <h3 class="service-title">Fashion Color</h3>
              <p class="service-desc">VIBRANT &amp; TRENDY HAIR SHADES</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1900</span>
          </a>

          <a class="service-card" href="booking.php?service=Balayage">
            <div>
              <h3 class="service-title">Balayage</h3>
              <p class="service-desc">HAND-PAINTED SUN-KISSED HIGHLIGHTS</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹2200</span>
          </a>

          <a class="service-card" href="booking.php?service=Ombre%20Color">
            <div>
              <h3 class="service-title">Ombre Color</h3>
              <p class="service-desc">GRADIENT BLEND FROM ROOT TO TIP</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹2400</span>
          </a>
        </div>
      </section>

      <section class="reveal" data-category="spa-treatment">
        <div class="category-header">
          <h2 class="eyebrow-tag">SPA &amp; TREATMENT</h2>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <a class="service-card" href="booking.php?service=Relaxation%20Spa">
            <div>
              <h3 class="service-title">Relaxation Spa</h3>
              <p class="service-desc">SOOTHING MASSAGE FOR COMPLETE RELAXATION</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
             <span class="service-price">₹800</span>
          </a>

          <a class="service-card" href="booking.php?service=Aroma%20Therapy">
            <div>
              <h3 class="service-title">Aroma Therapy</h3>
              <p class="service-desc">ESSENTIAL OILS FOR MIND &amp; BODY CALM</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1200</span>
          </a>

          <a class="service-card" href="booking.php?service=Head%20Spa">
            <div>
              <h3 class="service-title">Head Spa</h3>
              <p class="service-desc">DEEP CLEANSE &amp; SCALP REFRESH TREATMENT</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹700</span>
          </a>

          <a class="service-card" href="booking.php?service=Deep%20Tissue%20Spa">
            <div>
              <h3 class="service-title">Deep Tissue Spa</h3>
              <p class="service-desc">TARGETED MASSAGE FOR MUSCLE RELIEF</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1400</span>
          </a>

          <a class="service-card" href="booking.php?service=Scalp%20Treatment">
            <div>
              <h3 class="service-title">Scalp Treatment</h3>
              <p class="service-desc">PURIFYING SCALP EXFOLIATION &amp; RELIEF</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
            <span class="service-price">₹700</span>
          </a>

          <a class="service-card" href="booking.php?service=Hydrating%20Spa">
            <div>
              <h3 class="service-title">Hydrating Spa</h3>
              <p class="service-desc">MOISTURE BOOST FOR HEALTHY SKIN</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹900</span>
          </a>

          <a class="service-card" href="booking.php?service=Anti-Dandruff%20Treatment">
            <div>
              <h3 class="service-title">Anti-Dandruff Treatment</h3>
              <p class="service-desc">MEDICATED SCALP THERAPY</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹800</span>
          </a>

          <a class="service-card" href="booking.php?service=Protein%20Hair%20Treatment">
            <div>
              <h3 class="service-title">Protein Hair Treatment</h3>
              <p class="service-desc">STRENGTHENING KERATIN BOOST</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹1300</span>
          </a>
        </div>
      </section>

      <section class="reveal" data-category="beard">
        <div class="category-header">
          <h2 class="eyebrow-tag">BEARD</h2>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <a class="service-card" href="booking.php?service=Goatee%20Beard">
            <div>
              <h3 class="service-title">Goatee Beard</h3>
              <p class="service-desc">CLASSIC CHIN FOCUSED BEARD STYLE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹300</span>
          </a>

          <a class="service-card" href="booking.php?service=French%20Beard">
            <div>
              <h3 class="service-title">French Beard</h3>
              <p class="service-desc">STYLISH POINTED CHIN BEARD LOOK</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
             <span class="service-price">₹400</span>
          </a>

          <a class="service-card" href="booking.php?service=Full%20Beard%20Style">
            <div>
              <h3 class="service-title">Full Beard Style</h3>
              <p class="service-desc">DENSE &amp; WELL GROOMED BEARD FINISH</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹600</span>
          </a>

          <a class="service-card" href="booking.php?service=Italian%20Beard">
            <div>
              <h3 class="service-title">Italian Beard</h3>
              <p class="service-desc">SHARP &amp; ELEGANT FULL BEARD STYLE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹500</span>
          </a>

          <a class="service-card" href="booking.php?service=Stubble%20Beard">
            <div>
              <h3 class="service-title">Stubble Beard</h3>
              <p class="service-desc">SHORT &amp; NATURAL RUGGED LOOK</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹250</span>
          </a>

          <a class="service-card" href="booking.php?service=Corporate%20Beard">
            <div>
              <h3 class="service-title">Corporate Beard</h3>
              <p class="service-desc">CLEAN &amp; PROFESSIONAL BEARD STYLE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
             <span class="service-price">₹350</span>
          </a>

          <a class="service-card" href="booking.php?service=Anchor%20Beard">
            <div>
              <h3 class="service-title">Anchor Beard</h3>
              <p class="service-desc">DEFINED JAWLINE WITH SHARP EDGES</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹500</span>
          </a>

          <a class="service-card" href="booking.php?service=Boxed%20Beard">
            <div>
              <h3 class="service-title">Boxed Beard</h3>
              <p class="service-desc">NEAT SQUARE SHAPED BEARD STYLE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹400</span>
          </a>

          <a class="service-card" href="booking.php?service=Van%20Dyke%20Beard">
            <div>
              <h3 class="service-title">Van Dyke Beard</h3>
              <p class="service-desc">POINTED CHIN WITH DETACHED MUSTACHE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹450</span>
          </a>

          <a class="service-card" href="booking.php?service=Chin%20Strap%20Beard">
            <div>
              <h3 class="service-title">Chin Strap Beard</h3>
              <p class="service-desc">THIN DEFINED JAWLINE OUTLINE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
              <span class="service-price">₹300</span>
          </a>
        </div>
      </section>

      <section class="reveal" data-category="bridal-party">
        <div class="category-header">
          <h2 class="eyebrow-tag">BRIDAL / PARTY</h2>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <a class="service-card" href="booking.php?service=Special%20Bridal%20Package">
            <div>
              <h3 class="service-title">Special Bridal Package</h3>
              <p class="service-desc">COMPLETE BRIDAL LOOK WITH ADVANCED CARE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
             <span class="service-price">₹8000</span>
          </a>

          <a class="service-card" href="booking.php?service=Party%20Makeup%20%2B%20Hair">
            <div>
              <h3 class="service-title">Party Makeup + Hair</h3>
              <p class="service-desc">ELEGANT EVENT STYLING &amp; FINISH</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
            <span class="service-price">₹2500</span>
          </a>

          <a class="service-card" href="booking.php?service=Groom%20Package">
            <div>
              <h3 class="service-title">Groom Package</h3>
              <p class="service-desc">HAIRCUT, BEARD STYLING, SPA &amp; FACIAL</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
            <span class="service-price">₹1500</span>
          </a>

          <a class="service-card" href="booking.php?service=Bridal%20Package">
            <div>
              <h3 class="service-title">Bridal Package</h3>
              <p class="service-desc">FULL HAIR STYLING, MAKEUP &amp; SERUM CARE</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
            <span class="service-price">₹5000</span>
          </a>

          <a class="service-card" href="booking.php?service=Engagement%20Package">
            <div>
              <h3 class="service-title">Engagement Package</h3>
              <p class="service-desc">HAIR, MAKEUP &amp; DRAPING FOR RING CEREMONY</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
            <span class="service-price">₹3500</span>
          </a>

          <a class="service-card" href="booking.php?service=Sangeet%20Styling">
            <div>
              <h3 class="service-title">Sangeet Styling</h3>
              <p class="service-desc">GLAM HAIR &amp; MAKEUP FOR SANGEET NIGHT</p>
              <span class="book-mini-btn">Reserve Slot →</span>
            </div>
            <span class="service-price">₹2000</span>
          </a>
        </div>
      </section>

      <div style="margin-top: 60px; text-align: center;" class="reveal">
        <a href="booking.php" class="btn-primary">Book Your Appointment Now</a>
      </div>

    </div>
  </main>

<?php include 'backend/footer.php'; ?>

  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) entry.target.classList.add('active');
        });
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
    document.addEventListener('DOMContentLoaded', () => {
      const sections = Array.from(document.querySelectorAll('section[data-category]'));
      const cards = Array.from(document.querySelectorAll('.service-card'));
      const searchInput = document.getElementById('serviceSearch');
      const searchWrap = document.getElementById('filterSearchWrap');
      const clearSearchBtn = document.getElementById('clearSearch');
      const pills = Array.from(document.querySelectorAll('.filter-pill'));
      const noResults = document.getElementById('noResults');
      const resetBtn = document.getElementById('resetFilters');
      const filterCount = document.getElementById('filterCount');

      let activeCategory = 'all';

      pills.forEach(pill => {
        const cat = pill.dataset.category;
        const countEl = pill.querySelector('.pill-count');
        if (!countEl) return;
        const total = cat === 'all'
          ? cards.length
          : cards.filter(c => c.closest('section[data-category]').dataset.category === cat).length;
        countEl.textContent = '(' + total + ')';
      });

      function applyFilter() {
        const query = searchInput.value.trim().toLowerCase();
        searchWrap.classList.toggle('has-value', query.length > 0);

        let visibleTotal = 0;

        sections.forEach(section => {
          const sectionCategory = section.dataset.category;
          const categoryMatch = activeCategory === 'all' || sectionCategory === activeCategory;
          let sectionVisibleCount = 0;

          section.querySelectorAll('.service-card').forEach(card => {
            const title = (card.querySelector('.service-title')?.textContent || '').toLowerCase();
            const desc = (card.querySelector('.service-desc')?.textContent || '').toLowerCase();
            const queryMatch = !query || title.includes(query) || desc.includes(query);
            const visible = categoryMatch && queryMatch;
            card.classList.toggle('hidden', !visible);
            if (visible) sectionVisibleCount++;
          });

          section.classList.toggle('hidden', sectionVisibleCount === 0);
          visibleTotal += sectionVisibleCount;
        });

        noResults.classList.toggle('show', visibleTotal === 0);
        filterCount.textContent = visibleTotal === cards.length
          ? cards.length + ' services available'
          : 'Showing ' + visibleTotal + ' of ' + cards.length + ' services';
      }

      pills.forEach(pill => {
        pill.addEventListener('click', () => {
          pills.forEach(p => { p.classList.remove('active'); p.setAttribute('aria-pressed', 'false'); });
          pill.classList.add('active');
          pill.setAttribute('aria-pressed', 'true');
          activeCategory = pill.dataset.category;
          applyFilter();
        });
      });

      searchInput.addEventListener('input', applyFilter);

      clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.focus();
        applyFilter();
      });

      resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        pills.forEach(p => { p.classList.remove('active'); p.setAttribute('aria-pressed', 'false'); });
        const allPill = document.querySelector('.filter-pill[data-category="all"]');
        allPill.classList.add('active');
        allPill.setAttribute('aria-pressed', 'true');
        activeCategory = 'all';
        applyFilter();
      });

      applyFilter();
    });
  </script>

  <script>
  (function () {
    var h = document.querySelector('.site-header');
    if (!h) return;
    function set() { document.documentElement.style.setProperty('--hdr-h', h.offsetHeight + 'px'); }
    set();
    window.addEventListener('resize', set);
    window.addEventListener('load', set);
    if (window.ResizeObserver) new ResizeObserver(set).observe(h);
  })();
</script>
</body>
</html>