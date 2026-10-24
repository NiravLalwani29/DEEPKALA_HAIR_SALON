<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deepkala Hair Salon — Precision Haircuts & Grooming in Ghodasar, Ahmedabad</title>

  <!-- Favicon SVG Monogram -->
  <link rel="icon" type="image/png" href="assets/deepkala-emblem.png">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- FONT-FAMILY UPDATED: Fraunces + Karla → Playfair Display + Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

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

    a {
      color: inherit;
      text-decoration: none;
    }

    /* Container Utility */
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

    /* Typography */
    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }

    /* Eyebrow Tag */
    .eyebrow-tag {
      display: inline-block;
      background-color: var(--text-ink);
      color: var(--bg-cream);
      padding: 4px 12px;
      font-size: 0.72rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
    }

    .category-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1;
      height: 1px;
      background-color: var(--border-hairline);
    }

    /* Navbar */
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

    /* Buttons */
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
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(10, 8, 6, 0.35);
    }

    .btn-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: transparent;
      color: var(--text-ink);
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--text-ink);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-secondary:hover {
      background-color: var(--text-ink);
      color: var(--rose-deep);
      transform: translateY(-2px);
    }





    /* Hero Section */
    .hero-section {
      height: calc(100vh - 77px);
      min-height: 560px;
      max-height: calc(100vh - 77px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 0;
      text-align: center;
      position: relative;
      border-bottom: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      overflow: hidden;
      isolation: isolate;
    }

    .hero-section > .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      max-height: 100%;
    }

    @media (max-width: 640px) {
      .hero-section {
        height: calc(100svh - 77px);
        max-height: calc(100svh - 77px);
        min-height: 480px;
      }
    }

    .hero-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('assets/bg-d870505d.jpg');
      background-size: cover;
      background-position: center;
      z-index: -2;
    }

    .hero-section::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(10,8,6,0.78) 0%, rgba(201,166,107,0.42) 55%, rgba(10,8,6,0.85) 100%);
      z-index: -1;
    }

    .hero-section > .container {
      position: relative;
      z-index: 1;
    }


    .hero-title {
      font-size: clamp(2rem, 4.2vw, 3.75rem);
      font-weight: 700;
      line-height: 1.1;
      color: #FFFFFF;
      margin-bottom: 16px;
      letter-spacing: -0.02em;
    }

    .hero-tagline {
      font-size: clamp(0.95rem, 1.4vw, 1.125rem);
      color: #E4DCC8;
      max-width: 640px;
      margin: 0 auto 24px auto;
      font-weight: 400;
    }

    .hero-section .btn-secondary {
      color: #FFFFFF;
      border-color: #FFFFFF;
    }

    .hero-section .btn-secondary:hover {
      background-color: #1E1A14;
      color: var(--text-ink);
    }





    .hero-ctas {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    /* Highlights Section */
    .highlights-section {
      padding: 60px 0;
      border-bottom: 1px solid var(--border-hairline);
      background-color: var(--card-white);
    }

    .highlights-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
    }

    .highlight-card {
      padding: 24px;
      border: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      text-align: left;
    }

    .highlight-number {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--rose-accent);
      margin-bottom: 8px;
    }

    .highlight-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--text-ink);
    }

    .highlight-desc {
      font-size: 0.85rem;
      color: #9C9284;
      line-height: 1.5;
    }

    /* Services Preview Section */
    .services-preview-section {
      padding: 80px 0;
      border-bottom: 1px solid var(--border-hairline);
    }

    .services-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
      margin-bottom: 36px;
    }

    @media (min-width: 768px) {
      .services-grid {
        grid-template-columns: 1fr 1fr;
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
      transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .service-card:hover {
      transform: translateY(-2px);
      border-color: var(--bronze-taupe);
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
      color: #E4DCC8 !important;
    }


    .service-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 4px;
    }

    .service-desc {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 0.72rem;
      font-weight: 500;
      color: #9C9284;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .service-price {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-ink);
      white-space: nowrap;
    }

    .text-center {
      text-align: center;
    }

    /* Testimonials Preview */
    .testimonials-section {
      padding: 80px 0;
      background-color: var(--card-cream);
      border-bottom: 1px solid var(--border-hairline);
    }

    .reviews-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      margin-bottom: 36px;
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
    }

    .review-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }

    .avatar-circle {
      width: 44px;
      height: 44px;
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
    }

    .reviewer-name {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--text-ink);
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
      display: flex;
      gap: 4px;
    }

    .review-body {
      font-size: 0.925rem;
      color: #F0EBDF;
      line-height: 1.6;
    }

    /* CTA Banner */
    .cta-banner {
      padding: 70px 0;
      background-color: var(--rose-deep);
      color: var(--text-ink);
      text-align: center;
      position: relative;
      overflow: hidden;
      isolation: isolate;
    }

    .cta-banner::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('assets/bg-9fa6aade.jpg');
      background-size: cover;
      background-position: center;
      mix-blend-mode: screen;
      opacity: 0.22;
      pointer-events: none;
      z-index: 0;
    }

    .cta-banner > * {
      position: relative;
      z-index: 1;
    }

    .cta-banner h2 {
      font-size: clamp(1.8rem, 3.5vw, 2.5rem);
      font-weight: 700;
      margin-bottom: 12px;
      color: #FFFFFF;
    }

    .cta-banner p {
      font-size: 1rem;
      color: #9C9284;
      max-width: 560px;
      margin: 0 auto 28px auto;
    }

    .btn-cta-light {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background-color: var(--rose-accent);
      color: #15130E;
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: none;
      transition: background-color 0.2s ease;
    }

    .btn-cta-light:hover {
      background-color: var(--rose-accent-hover);
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

    /* Scroll Reveal */
    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal {
        opacity: 1;
        transform: none;
        transition: none;
      }
    }

    /* Mobile Responsive Nav Drawer */
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

  <!-- Site Header / Navbar -->
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
          <li><a href="index.php" class="nav-link active" aria-current="page"><span class="nav-underline" aria-hidden="true"></span>Home</a></li>
          <li><a href="services.php" class="nav-link">Services</a></li>
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
  <!-- Main Content -->
  <main id="main-content" tabindex="-1">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container reveal">
        <h1 class="hero-title font-serif">Master Craftsmanship in Hair Styling & Grooming</h1>
        <p class="hero-tagline">Precision cuts, modern skin fades, relaxing hair treatments, and sharp beard styling crafted for the modern individual.</p>
        
        <div class="hero-ctas">
          <a href="booking.php" class="btn-primary">Book Appointment</a>
          <a href="services.php" class="btn-secondary">View Services & Pricing</a>
        </div>
      </div>
    </section>

    <!-- Highlights Strip -->
    <section class="highlights-section">
      <div class="container">
        <div class="highlights-grid">
          <div class="highlight-card reveal">
            <div class="highlight-number">01</div>
            <h2 class="highlight-title">Precision Cuts</h2>
            <p class="highlight-desc">Custom tailored scissors and clipper work designed specifically for your scalp shape and style.</p>
          </div>
          <div class="highlight-card reveal">
            <div class="highlight-number">02</div>
            <h2 class="highlight-title">Master Hair Styling</h2>
            <p class="highlight-desc">Tailored grooming, sharp skin fades, and hair treatments crafted with passion and precision.</p>
          </div>
          <div class="highlight-card reveal">
            <div class="highlight-number">03</div>
            <h2 class="highlight-title">Clean & Hygienic</h2>
            <p class="highlight-desc">Sanitized blades, fresh towels, and a spotless setting for every single customer.</p>
          </div>
          <div class="highlight-card reveal">
            <div class="highlight-number">04</div>
            <h2 class="highlight-title">Daily 9 AM – 9 PM</h2>
            <p class="highlight-desc">Open 7 days a week at Ghodasar, Cadila Road for convenient walk-ins and bookings.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Preview Section -->
    <section class="services-preview-section">
      <div class="container">
        <div class="category-header reveal">
          <span class="eyebrow-tag">POPULAR SERVICES</span>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Classic Haircut</h3>
              <p class="service-desc">PRECISION SCISSOR & CLIPPER CUT</p>
            </div>
            <span class="service-price">₹150</span>
          </div>

          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Skin Fade</h3>
              <p class="service-desc">CLEAN, BLENDED FADE WITH SHARP FINISH</p>
            </div>
            <span class="service-price">₹250</span>
          </div>

          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Hair Spa</h3>
              <p class="service-desc">DEEP NOURISHING CRÈME MASSAGE & STEAM</p>
            </div>
            <span class="service-price">₹600</span>
          </div>

          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Beard Shape-up</h3>
              <p class="service-desc">RAZOR LINING & CONTOUR DEFINITION</p>
            </div>
            <span class="service-price">₹150</span>
          </div>
        </div>

        <div class="text-center reveal">
          <a href="services.php" class="btn-secondary">Explore All Services & Pricing</a>
        </div>
      </div>
    </section>

    <!-- Testimonials Preview -->
    <section class="testimonials-section">
      <div class="container">
        <div class="category-header reveal">
          <span class="eyebrow-tag">CLIENT FEEDBACK</span>
          <div class="hairline"></div>
        </div>

        <div class="reviews-grid">
          <div class="review-card reveal">
            <div class="review-header">
              <div class="avatar-circle">RP</div>
              <div>
                <h3 class="reviewer-name">Rahul Patel</h3>
                <div class="reviewer-service">SKIN FADE & BEARD TRIM</div>
              </div>
            </div>
            <div class="star-rating">
              ★★★★★
            </div>
            <p class="review-body">"Best fade in Ghodasar! Deepesh bhai is extremely skilled and takes time to get the lines perfect. Clean salon and very comfortable atmosphere."</p>
          </div>

          <div class="review-card reveal">
            <div class="review-header">
              <div class="avatar-circle">PS</div>
              <div>
                <h3 class="reviewer-name">Parth Shah</h3>
                <div class="reviewer-service">HAIR SPA & STYLING</div>
              </div>
            </div>
            <div class="star-rating">
              ★★★★★
            </div>
            <p class="review-body">"Got a hair spa and classic haircut before an event. My hair felt so smooth and healthy. Outstanding value for money at ₹600."</p>
          </div>
        </div>

        <div class="text-center reveal">
          <a href="reviews.php" class="btn-secondary">Read More Client Reviews</a>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="cta-banner">
      <div class="container reveal">
        <h2 class="font-serif">Ready for Your Next Fresh Look?</h2>
        <p>Walk-ins are always welcome, or send us a quick message on WhatsApp to reserve your slot directly.</p>
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
          <a href="booking.php" class="btn-cta-light">Book on WhatsApp</a>
          <a href="tel:+919924620958" class="btn-secondary" style="border-color: #FFFFFF;">Call +91 99246 20958</a>
        </div>
      </div>
    </section>
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

  <!-- Floating WhatsApp Button -->
  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <!-- Shared Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Scroll Reveal Observer
      const observerOptions = { threshold: 0.1 };
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('active');
          }
        });
      }, observerOptions);

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

        // Close when clicking outside menu
        document.addEventListener('click', (e) => {
          if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) {
            toggleMenu(false);
          }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            toggleMenu(false);
          }
        });

        // Close when clicking a menu link
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
    });
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
