<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Reviews & Feedback — Deepkala Hair Salon, Ahmedabad</title>

  <!-- Favicon SVG Monogram -->
  <link rel="icon" type="image/png" href="assets/deepkala-emblem.png">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- FONT-FAMILY UPDATED: Fraunces + Karla → Playfair Display + Inter -->
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:wght@400..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:wght@400..900&display=swap"></noscript>

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg-cream: #15130E;
      --text-ink: #F3EEE2;
      --rose-accent: #C9A66B;
      --rose-accent-hover: #B8935A;
      --rose-deep: #0A0806;
      --bronze-taupe: #A9884C;
      --card-white: #1E1A14;
      --card-cream: #221E17;
      --border-hairline: #7A6C55;
      --whatsapp-green: #25D366;
    }

    html {
      scroll-behavior: smooth;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
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

    a { color: inherit; text-decoration: none; }

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

    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }

    /* Header */
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

    .brand-logo { display: flex; align-items: center; gap: 12px; }

    .logo-text-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0;
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      color: var(--text-ink);
      line-height: 1.1;
    }

    .logo-text-sub {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--bronze-taupe);
    }

    .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; }

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

    .nav-link:hover, .nav-link.active { color: var(--rose-accent); }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 100%; height: 2px;
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
      color: #15130E;
      border: 1px solid var(--rose-accent);
      border-radius: 4px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .header-book-btn:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      color: #15130E;
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
      color: #15130E !important;
      font-size: 0.85rem;
      font-weight: 600;
      border-radius: 4px;
      transition: background-color 0.2s ease;
      box-shadow: 0 2px 8px rgba(37, 211, 102, 0.25);
    }

    .btn-whatsapp-cta:hover {
      background-color: #20BA56;
      color: #15130E !important;
    }

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
      color: var(--text-ink);
    }

    /* Page Hero */
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
        repeating-linear-gradient(135deg, rgba(243,238,226,0.04) 0px, rgba(243,238,226,0.04) 1px, transparent 1px, transparent 9px),
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
      color: #B3AA98;
      max-width: 620px;
      margin: 0 auto;
    }





    /* Reviews Grid & Cards */
    .eyebrow-tag {
      display: inline-block;
      background-color: var(--text-ink);
      color: var(--bg-cream);
      padding: 6px 14px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
    }

    .category-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-top: 48px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1; height: 1px;
      background-color: var(--border-hairline);
    }

    .reviews-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
    }

    @media (min-width: 768px) {
      .reviews-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .review-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .review-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }

    .avatar-circle {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      background-color: var(--card-cream);
      border: 1px solid var(--bronze-taupe);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-weight: 700;
      color: var(--rose-accent);
      font-size: 1.1rem;
      flex-shrink: 0;
      overflow: hidden;
    }

    .avatar-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .reviewer-name {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-ink);
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.65);
    }

    .reviewer-service {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--bronze-taupe);
    }

    .star-rating {
      color: var(--rose-accent);
      margin-bottom: 12px;
      font-size: 1.1rem;
    }

    .review-body {
      font-size: 0.925rem;
      color: #F0EBDF;
      line-height: 1.6;
      margin-bottom: 16px;
    }

    .review-photo-preview {
      width: 100%;
      max-height: 200px;
      object-fit: cover;
      border: 1px solid var(--border-hairline);
      margin-top: 12px;
    }

    .review-date {
      font-size: 0.72rem;
      color: #9C9284;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    /* Review Form Section */
    .form-section {
      background-color: var(--card-cream);
      border: 1px solid var(--border-hairline);
      padding: 40px;
      margin-top: 60px;
    }

    .form-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .form-desc {
      font-size: 0.9rem;
      color: #9C9284;
      margin-bottom: 16px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
    }

    @media (min-width: 768px) {
      .form-grid { grid-template-columns: 1fr 1fr; }
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-group.full {
      grid-column: 1 / -1;
    }

    .form-label {
      font-size: 0.78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-ink);
    }

    .form-input, .form-select, .form-textarea {
      padding: 12px 16px;
      border: 1px solid var(--border-hairline);
      background-color: #1E1A14;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 16px;
      color: var(--text-ink);
      width: 100%;
      min-height: 44px;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: 2px solid var(--rose-accent);
      border-color: var(--rose-accent);
    }

    /* Star Picker */
    .star-picker-fieldset {
      border: 0;
      margin: 0;
      padding: 0;
      min-width: 0;
    }

    .star-picker-fieldset legend {
      padding: 0;
      margin-bottom: 8px;
    }

    .star-picker {
      display: flex;
      gap: 8px;
      font-size: 1.5rem;
      cursor: pointer;
      color: #6B6152;
    }

    .star-picker .star {
      transition: color 0.15s ease;
      position: relative;
      cursor: pointer;
    }

    .star-picker .star.active, .star-picker .star:hover {
      color: var(--rose-accent);
    }

    .star-radio {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }

    .star-radio:focus-visible + .star {
      outline: 2px solid var(--rose-accent, #C9A66B);
      outline-offset: 3px;
      border-radius: 3px;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: var(--rose-accent);
      color: #15130E;
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--rose-accent);
      cursor: pointer;
      transition: all 0.2s ease;
      min-height: 44px;
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
    }

    /* keep footer at page bottom on short pages */
    main { flex: 1 0 auto; }
    /* Floating WhatsApp Button */
    .whatsapp-float {
      position: fixed;
      bottom: max(20px, env(safe-area-inset-bottom, 20px));
      right: max(20px, env(safe-area-inset-right, 20px));
      width: 52px;
      height: 52px;
      min-width: 48px;
      min-height: 48px;
      background-color: var(--whatsapp-green);
      color: #15130E;
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
      opacity: 0; transform: translateY(20px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal.active { opacity: 1; transform: translateY(0); }

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
      /* ===== Deepkala Membership & Auth (shared) ===== */
    .dk-price-old {
      text-decoration: line-through;
      color: #8A8171;
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
      color: #FFD666;
      border: 1px solid rgba(255, 214, 102, 0.45);
    }
.nav-member-badge { margin-left: 8px; font-size: 0.55rem !important; padding: 2px 7px !important; vertical-align: middle; }


  </style>
  <style>
    /* Smooth native page transition (no JS, no opacity hacks -> no flicker) */
    @view-transition {
      navigation: auto;
    }
  </style>
  <style>
    /* Accessibility: skip link */
    .skip-link {
      position: absolute;
      top: -48px;
      left: 8px;
      z-index: 3000;
      background-color: var(--rose-accent, #C9A66B);
      color: #15130E;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
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

    /* Accessibility: visually-hidden utility (screen-reader only) */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }

    /* Accessibility: consistent visible focus styles across interactive elements */
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
      /* Accessibility: if JS fails to load, scroll-reveal content must still be visible */
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
  <!-- Header -->
  <header class="site-header" id="siteHeader">
    <div class="container header-inner">
      <a href="index.php" class="brand-logo" aria-label="Deepkala Hair Salon">
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
          <li><a href="reviews.php" class="nav-link active" aria-current="page"><span class="nav-underline" aria-hidden="true"></span>Reviews</a></li>
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
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container reveal">
<h1 class="font-serif">Client Reviews &amp; Stories</h1>
      </div>
    </section>

    <div class="container" style="padding-bottom: 60px;">
      
      <!-- Reviews Header -->
      <div class="category-header reveal">
        <span class="eyebrow-tag">VERIFIED CLIENT REVIEWS</span>
        <div class="hairline"></div>
      </div>

      <!-- Pre-written Reviews Grid -->
      <div class="reviews-grid" id="reviewsGrid">
        
        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">RP</div>
              <div>
                <h3 class="reviewer-name">Rahul Patel</h3>
                <div class="reviewer-service">SKIN FADE &amp; BEARD SHAPE-UP</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"Best fade in Ghodasar! Deepesh bhai is extremely skilled and takes time to get the lines perfect. Clean salon and very comfortable atmosphere."</p>
          </div>
          <div class="review-date">JUNE 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">PS</div>
              <div>
                <h3 class="reviewer-name">Parth Shah</h3>
                <div class="reviewer-service">HAIR SPA &amp; CLASSIC CUT</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"Got a hair spa and classic haircut before an event. My hair felt so smooth and healthy. Outstanding value for money at ₹600."</p>
          </div>
          <div class="review-date">MAY 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">HJ</div>
              <div>
                <h3 class="reviewer-name">Hetal Joshi</h3>
                <div class="reviewer-service">KERATIN TREATMENT</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"The Keratin treatment at Deepkala completely changed my hair texture. No frizz even in summer! Very attentive service."</p>
          </div>
          <div class="review-date">MAY 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">AV</div>
              <div>
                <h3 class="reviewer-name">Amit Varma</h3>
                <div class="reviewer-service">BEARD TRIM &amp; HOT TOWEL SHAVE</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"The hot towel shave experience is super relaxing. Razor work is extremely precise and painless. Highly recommended for beard lovers."</p>
          </div>
          <div class="review-date">APRIL 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">VS</div>
              <div>
                <h3 class="reviewer-name">Vikrant Solanki</h3>
                <div class="reviewer-service">UNDERCUT &amp; HAIR WASH</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"Regular client for 2 years now. Always consistent quality, hygienic tools, and friendly staff. Never disappointed."</p>
          </div>
          <div class="review-date">APRIL 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">SM</div>
              <div>
                <h3 class="reviewer-name">Sneha Mehta</h3>
                <div class="reviewer-service">GLOBAL COLOR &amp; STYLING</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"They matched my exact requested color tone. Very clean work and friendly consultation before starting."</p>
          </div>
          <div class="review-date">MARCH 2026</div>
        </div>

      </div>

      <!-- Write a Review Form -->
      <section class="form-section reveal">
        <h2 class="form-title">Write a Review</h2>
        <p class="form-desc">Visited Deepkala Hair Salon recently? Share your feedback and optional photo with our community.</p>

        <div id="reviewSuccess" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

        <form id="reviewForm" onsubmit="handleReviewSubmit(event)">
          <div class="form-grid">
            
            <div class="form-group">
              <label class="form-label" for="revName">Your Full Name *</label>
              <input type="text" id="revName" class="form-input" placeholder="e.g. Chirag Patel" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="revService">Service Used *</label>
              <select id="revService" class="form-select" required>
                <option value="" disabled selected>Select a service</option>
                <option value="">--- Cut & Style ---</option>
                <option value="Buzz Cut — (₹200)">Buzz Cut — ₹200</option>
                  <option value="Crew Cut — (₹250)">Crew Cut — ₹250</option>
                  <option value="Fade Cut — (₹350)">Fade Cut — ₹350</option>
                  <option value="Undercut — (₹400)">Undercut — ₹400</option>
                  <option value="Mid Fade — (₹500)">Mid Fade — ₹500</option>
                  <option value="Quiff — (₹450)">Quiff — ₹450</option>
                  <option value="Side Part — (₹300)">Side Part — ₹300</option>
                  <option value="French Crop — (₹350)">French Crop — ₹350</option>
                  <option value="Texture Crop — (₹450)">Texture Crop — ₹450</option>
                  <option value="Ceaser Cut — (₹300)">Ceaser Cut — ₹300</option>
                  <option value="">--- Coloring ---</option>
                  <option value="Root Touch-Up — (₹3200)">Root Touch-Up — ₹3200</option>
                  <option value="Highlights — (₹1700)">Highlights — ₹1700</option>
                  <option value="Low Lights — (₹1500)">Low Lights — ₹1500</option>
                  <option value="Gloss Treatment — (₹2000)">Gloss Treatment — ₹2000</option>
                  <option value="Color Correction — (₹1600)">Color Correction — ₹1600</option>
                  <option value="Fasion Color — (₹1900)">Fasion Color — ₹1900</option>
                  <option value="">--- Spa & Touchup ---</option>
                  <option value="Relaxation Spa — (₹800)">Relaxation Spa — ₹800</option>
                  <option value="Aroma Treatment — (₹1200)">Aroma Treatment — ₹1200</option>
                  <option value="Head Spa — (₹700)">Head Spa — ₹700</option>
                  <option value="Deep Tissue Spa — (₹1400)">Deep Tissue Spa — ₹1400</option>
                  <option value="Scalp Treatment — (₹700)">Scalp Treatment — ₹700</option>
                  <option value="Hydrating Spa — (₹900)">Hydrating Spa — ₹900</option>
                  <option value="">--- Beard Grooming ---</option>
                  <option value="Goatee Beard — (₹500)">Goatee Beard — ₹500</option>
                  <option value="French Beard — (₹400)">French Beard — ₹400</option>
                  <option value="Full Beard Style — (₹600)">Full Beard Style — ₹600</option>
                  <option value="Italian Beard — (₹500)">Italian Beard — ₹500</option>
                  <option value="Stubble Beard — (₹250)">Stubble Beard — ₹250</option>
                  <option value="Corporate Beard — (₹350)">Corporate Beard — ₹350</option>
                  <option value="Anchor Beard — (₹500)">Anchor Beard — ₹500</option>
                  <option value="Boxed Beard — (₹400)">Boxed Beard — ₹400</option>
                  <option value="">--- Bridal & Party ---</option>
                  <option value="Special Bridal Package — (₹8000)">Special Bridal Package — ₹8000</option>
                  <option value="Bridal Package (₹5000)">Bridal Package — ₹5000</option>
                  <option value="Party Makeup + Hair (₹2500)">Party Makeup + Hair — ₹2500</option>
                  <option value="Groom Package (₹1500)">Groom Package — ₹1500</option>
              </select>
            </div>

            <div class="form-group">
              <fieldset class="star-picker-fieldset">
                <legend class="form-label">Your Rating *</legend>
                <div class="star-picker" id="starPicker">
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar1" value="1" data-value="1">
                  <label class="star active" for="revStar1" data-value="1" aria-label="1 star">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar2" value="2" data-value="2">
                  <label class="star active" for="revStar2" data-value="2" aria-label="2 stars">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar3" value="3" data-value="3">
                  <label class="star active" for="revStar3" data-value="3" aria-label="3 stars">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar4" value="4" data-value="4">
                  <label class="star active" for="revStar4" data-value="4" aria-label="4 stars">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar5" value="5" data-value="5" checked>
                  <label class="star active" for="revStar5" data-value="5" aria-label="5 stars">★</label>
                </div>
              </fieldset>
              <input type="hidden" id="revRating" value="5">
            </div>

            <div class="form-group">
              <label class="form-label" for="revPhoto">Optional Hair/Style Photo</label>
              <input type="file" id="revPhoto" class="form-input" accept="image/*" onchange="previewReviewPhoto(event)">
            </div>

            <div class="form-group full">
              <label class="form-label" for="revMessage">Your Review Message *</label>
              <textarea id="revMessage" class="form-textarea" rows="4" placeholder="Tell us about your haircut, service quality, and experience..." required></textarea>
            </div>

          </div>

          <!-- Photo Image Preview Container -->
          <div id="photoPreviewContainer" style="display: none; margin-top: 16px;">
            <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--bronze-taupe); margin-bottom: 6px;">Image Preview:</div>
            <img id="photoPreviewImg" style="max-height: 180px; border: 1px solid var(--border-hairline);" alt="Review Photo Preview">
          </div>

          <div style="margin-top: 24px;">
            <button type="submit" class="btn-primary">Post Review</button>
          </div>

        </form>
      </section>

    </div>
  </main>

  <!-- Footer -->
  <footer class="dk-footer" id="site-footer">
    <div class="container">
      <div class="dk-footer-main">

        <div class="dk-footer-brand">
          <a href="index.php" class="brand-logo" aria-label="Deepkala Hair Salon"> <img src="assets/deepkala-emblem.png" width="34" height="34" alt="Deepkala Hair Salon" style="border-radius:6px;object-fit:cover;"> <div> <div class="logo-text-title">DEEPKALA</div> <div class="logo-text-sub">HAIR SALON</div> </div> </a>
          <p class="dk-footer-desc">Master craftsmanship, precision haircuts, and dedicated personal grooming in Ghodasar, Ahmedabad.</p>
          <div class="dk-footer-social">
            <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" title="WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></a>
            <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
            <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
          </div>
        </div>

        <nav class="dk-footer-col" aria-label="Quick Links">
          <h3 class="dk-footer-title">Quick Links</h3>
          <ul class="dk-footer-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="reviews.php">Client Reviews</a></li>
            <li><a href="about.php">About</a></li>
          </ul>
        </nav>

        <nav class="dk-footer-col" aria-label="Book and Visit">
          <h3 class="dk-footer-title">Book &amp; Visit</h3>
          <ul class="dk-footer-list">
            <li><a href="booking.php">Reserve Slot</a></li>
            <li><a href="membership.php">Membership</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </nav>

        <div class="dk-footer-col dk-footer-contact">
          <h3 class="dk-footer-title">Salon Info</h3>
          <ul class="dk-footer-info">
            <li><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 19.5s-6.5-5.4-6.5-10.5a6.5 6.5 0 0113 0c0 5.1-6.5 10.5-6.5 10.5zM12 6.5a2.5 2.5 0 100 5 2.5 2.5 0 000-5z"/><ellipse cx="12" cy="21.6" rx="5" ry="1.1" opacity=".55"/></svg><div><small>Salon Address</small><a href="https://www.google.com/maps/search/?api=1&query=2+Jayvimal+Society+Cadila+Road+Ghodasar+Ahmedabad+380050" target="_blank" rel="noopener noreferrer" title="Open in Google Maps">2 Jayvimal Society, Cadila Road, Ghodasar, Ahmedabad – 380050</a></div></li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg><div><small>Phone No</small><a href="tel:+919924620958">+91 99246 20958</a></div></li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><div><small>Timings</small>9:00 AM – 9:00 PM, Daily</div></li>
          </ul>
        </div>

      </div>

      <hr class="dk-footer-hr">
      <p class="dk-footer-copy">© 2026 Deepkala Hair Salon. All rights reserved.</p>
    </div>
  </footer>

  <!-- Floating WhatsApp -->
  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
    let currentPhotoDataUrl = null;

    document.addEventListener('DOMContentLoaded', () => {
      // Intersection Observer
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) entry.target.classList.add('active');
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

      // Mobile Menu Toggle & Accessibility
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

      // Sticky Header Shadow
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

      // Star Picker Logic
      const starRadios = document.querySelectorAll('#starPicker .star-radio');
      const starLabels = document.querySelectorAll('#starPicker .star');
      const ratingInput = document.getElementById('revRating');

      starRadios.forEach(radio => {
        radio.addEventListener('change', () => {
          const val = parseInt(radio.value, 10);
          ratingInput.value = val;
          starLabels.forEach(label => {
            const labelVal = parseInt(label.getAttribute('data-value'), 10);
            label.classList.toggle('active', labelVal <= val);
          });
        });
      });
    });

    // Preview uploaded photo via FileReader
    function previewReviewPhoto(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          currentPhotoDataUrl = e.target.result;
          const container = document.getElementById('photoPreviewContainer');
          const img = document.getElementById('photoPreviewImg');
          img.src = currentPhotoDataUrl;
          container.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }

    // Handle Review Form Submission (Appends to top of review list in session)
    function handleReviewSubmit(e) {
      e.preventDefault();

      const name = document.getElementById('revName').value.trim();
      const service = document.getElementById('revService').value;
      const ratingVal = parseInt(document.getElementById('revRating').value);
      const message = document.getElementById('revMessage').value.trim();

      if (!name || !service || !message) return;

      // Extract Initials
      const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'CL';

      // Build Star String
      let starsHtml = '';
      for (let i = 0; i < 5; i++) {
        starsHtml += (i < ratingVal) ? '★' : '☆';
      }

      // Create Review Element
      const reviewCard = document.createElement('div');
      reviewCard.className = 'review-card reveal active';
      reviewCard.style.border = '2px solid var(--rose-accent)';

      let photoHtml = '';
      if (currentPhotoDataUrl) {
        photoHtml = `<img src="${currentPhotoDataUrl}" class="review-photo-preview" alt="Customer haircut photo preview">`;
      }

      reviewCard.innerHTML = `
        <div>
          <div class="review-header">
            <div class="avatar-circle">${initials}</div>
            <div>
              <h3 class="reviewer-name">${name}</h3>
              <div class="reviewer-service">${service.toUpperCase()}</div>
            </div>
          </div>
          <div class="star-rating" role="img" aria-label="${ratingVal} out of 5 stars">${starsHtml}</div>
          <p class="review-body">"${message}"</p>
          ${photoHtml}
        </div>
        <div class="review-date">JUST NOW (NEW SESSION REVIEW)</div>
      `;

      // Prepend to grid
      const grid = document.getElementById('reviewsGrid');
      grid.insertBefore(reviewCard, grid.firstChild);

      // Reset Form
      document.getElementById('reviewForm').reset();
      document.getElementById('photoPreviewContainer').style.display = 'none';
      currentPhotoDataUrl = null;

      // Reset Stars
      const stars = document.querySelectorAll('#starPicker .star');
      stars.forEach(s => s.classList.add('active'));
      document.getElementById('revRating').value = 5;

      // Smooth scroll to new review
      reviewCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

      // Announce success to assistive tech
      const successRegion = document.getElementById('reviewSuccess');
      if (successRegion) successRegion.textContent = 'Thank you! Your review has been posted.';
    }
  </script>

  <script>
    /* ===== Deepkala Membership & Auth (client-side demo, no backend) ===== */
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

    // Auto-apply member discount to every element with class "service-price"
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

    // Render the header auth area: Login link, or account pill with member badge + logout
    function dkRenderAuthNav() {}

    document.addEventListener('DOMContentLoaded', () => {
      dkApplyMemberPricing();
      dkRenderAuthNav();
    });
  </script>

  </body>
</html>
