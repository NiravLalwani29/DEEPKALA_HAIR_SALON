<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us — Deepkala Hair Salon, Ghodasar, Ahmedabad</title>

  <meta name="description" content="Deepkala Hair Salon in Ghodasar, Ahmedabad: 12+ years of haircuts, beard grooming, hair spa and bridal makeup. Open daily 9 AM–9 PM. Cuts from ₹100.">
  <meta property="og:title" content="About Deepkala Hair Salon — Ghodasar, Ahmedabad">
  <meta property="og:description" content="12+ years of craft: haircuts, beard grooming, hair spa and bridal makeup in Ghodasar.">
  <meta property="og:type" content="website">
  <script type="application/ld+json">
  {"@context":"https://schema.org","@type":"HairSalon","name":"Deepkala Hair Salon","telephone":"+919924620958","priceRange":"₹","address":{"@type":"PostalAddress","streetAddress":"2 Jayvimal Society, Cadila Road, Ghodasar","addressLocality":"Ahmedabad","postalCode":"380050","addressCountry":"IN"},"openingHours":"Mo-Su 09:00-21:00"}
  </script>

  <!-- Favicon -->
  <link rel="preconnect" href="https://images.unsplash.com">
  <link rel="preload" as="image" href="https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=1600&q=70&auto=format&fit=crop">
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
      --gold-glow: #FFD666;
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
      overflow-x: clip; /* clip (not hidden) keeps the sticky header working */
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
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }

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

    /* ============================================================
       ABOUT PAGE — NEW ARCHITECTURE
       Cinematic hero → stat strip → vertical story timeline →
       numbered values list → team filmstrip → split visit band
       ============================================================ */

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
      margin-top: 32px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1; height: 1px;
      background-color: var(--border-hairline);
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
      margin-top: 20px;
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(10,8,6,0.35);
    }


    /* ---------- 1. Cinematic full-bleed hero ---------- */
    .ab-hero {
      position: relative;
      min-height: 78vh;
      display: flex;
      align-items: flex-end;
      overflow: hidden;
      border-bottom: 1px solid var(--border-hairline);
    }

    .ab-hero-bg {
      position: absolute; inset: 0;
      background: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=1600&q=70&auto=format&fit=crop') center 30% / cover no-repeat;
      filter: grayscale(20%) contrast(1.05);
      transform: scale(1.02);
    }

    .ab-hero-scrim {
      position: absolute; inset: 0;
      background:
        linear-gradient(180deg, rgba(10,8,6,0.35) 0%, rgba(10,8,6,0.55) 55%, var(--bg-cream) 96%),
        linear-gradient(90deg, rgba(10,8,6,0.55) 0%, rgba(10,8,6,0.05) 55%);
    }

    .ab-hero-inner {
      position: relative;
      z-index: 1;
      padding: 180px 0 96px 0;
      max-width: 760px;
    }

    .ab-hero-inner .eyebrow-tag {
      background-color: var(--rose-accent);
      color: #15130E;
      margin-bottom: 20px;
    }

    .ab-hero h1 {
      font-size: clamp(2.6rem, 6vw, 4.4rem);
      font-weight: 700;
      line-height: 1.05;
      color: var(--text-ink);
      margin-bottom: 44px;
      text-shadow: 0 2px 24px rgba(0,0,0,0.4);
    }

    .ab-hero p {
      font-size: 1.08rem;
      color: #E4DCC9;
      max-width: 540px;
      margin-bottom: 28px;
    }

    .ab-hero-actions {
      display: flex;
      align-items: center;
      gap: 28px;
      flex-wrap: wrap;
    }

    .ab-hero-actions .btn-primary { margin-top: 0; }

    .ab-scroll-cue {
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--text-ink);
      opacity: 0.85;
      transition: opacity 0.2s ease;
    }
    .ab-scroll-cue:hover { opacity: 1; color: var(--rose-accent); }

    @media (max-width: 640px) {
      .ab-hero { min-height: 64vh; }
      .ab-hero-inner { padding: 120px 0 64px 0; }
    }

    /* ---------- 2. Stat strip ---------- */
    .ab-stat-strip {
      background-color: var(--card-white);
      border-bottom: 1px solid var(--border-hairline);
    }

    .ab-stat-strip-inner {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
    }

    .ab-stat {
      padding: 28px 20px;
      text-align: center;
      border-right: 1px solid var(--border-hairline);
    }
    .ab-stat:last-child { border-right: none; }

    .ab-stat span {
      display: block;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: clamp(1.5rem, 3vw, 2.1rem);
      font-weight: 700;
      color: var(--rose-accent);
      line-height: 1;
      margin-bottom: 6px;
    }

    .ab-stat .ab-stat-label {
      font-size: 0.68rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #9C9284;
    }

    @media (max-width: 640px) {
      .ab-stat-strip-inner { grid-template-columns: repeat(2, 1fr); }
      .ab-stat:nth-child(2) { border-right: none; }
      .ab-stat:nth-child(1), .ab-stat:nth-child(2) { border-bottom: 1px solid var(--border-hairline); }
    }

    .ab-section { padding: 2px 0; }
    /* .ab-section + .ab-section { border-top: 1px solid var(--border-hairline); } */

    .ab-section-title {
      font-size: clamp(1.6rem, 3vw, 2.2rem);
      font-weight: 700;
      color: var(--text-ink);
      max-width: 640px;
      margin-bottom: 36px;
    }

    /* ---------- 3. Story timeline ---------- */
    .ab-timeline {
      position: relative;
      max-width: 780px;
    }

    .ab-timeline::before {
      content: '';
      position: absolute;
      top: 6px; bottom: 6px; left: 27px;
      width: 1px;
      background: linear-gradient(180deg, var(--rose-accent) 0%, var(--border-hairline) 100%);
      opacity: 0.5;
    }

    .ab-timeline-item {
      position: relative;
      display: flex;
      gap: 28px;
      padding-bottom: 44px;
    }
    .ab-timeline-item:last-child { padding-bottom: 0; }

    .ab-timeline-marker {
      flex-shrink: 0;
      width: 56px; height: 56px;
      border-radius: 50%;
      background-color: var(--bg-cream);
      border: 1px solid var(--rose-accent);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-weight: 700;
      font-size: 1.05rem;
      color: var(--rose-accent);
      position: relative;
      z-index: 1;
    }

    .ab-timeline-content { padding-top: 8px; }

    .ab-timeline-content h3 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 10px;
    }

    .ab-timeline-content p {
      font-size: 0.95rem;
      line-height: 1.8;
      color: #B3AA98;
      max-width: 620px;
    }

    @media (max-width: 560px) {
      .ab-timeline::before { left: 21px; }
      .ab-timeline-marker { width: 44px; height: 44px; font-size: 0.9rem; }
      .ab-timeline-item { gap: 18px; }
    }

    /* ---------- 4. Values — numbered list ---------- */
    .ab-values-list {
      display: flex;
      flex-direction: column;
    }

    .ab-value-row {
      display: grid;
      grid-template-columns: 88px 1fr;
      gap: 24px;
      padding: 28px 0;
      border-top: 1px solid var(--border-hairline);
      align-items: start;
      transition: padding-left 0.2s ease;
    }
    /* .ab-value-row:last-child { border-bottom: 1px solid var(--border-hairline); } */

    .ab-value-row:hover { padding-left: 8px; }

    .ab-value-num {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--bronze-taupe);
      line-height: 1;
      transition: color 0.2s ease;
    }
    .ab-value-row:hover .ab-value-num { color: var(--rose-accent); }

    .ab-value-body { display: grid; grid-template-columns: 1fr 1.4fr; gap: 20px; align-items: baseline; }

    .ab-value-body h3 {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-ink);
    }

    .ab-value-body p {
      font-size: 0.9rem;
      color: #9C9284;
      line-height: 1.65;
    }

    @media (max-width: 720px) {
      .ab-value-body { grid-template-columns: 1fr; gap: 8px; }
      .ab-value-row { grid-template-columns: 56px 1fr; }
      .ab-value-num { font-size: 1.5rem; }
    }

    /* ---------- 5. Team filmstrip (flip cards) ---------- */
    .ab-team-scroll {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      padding-bottom: 12px;
    }
    .ab-team-scroll::-webkit-scrollbar { height: 6px; }
    .ab-team-scroll::-webkit-scrollbar-thumb { background: var(--border-hairline); border-radius: 6px; }

    /* Flip card shell: gives the 3D stage its depth and size */
    .ab-team-card {
      scroll-snap-align: start;
      width: 100%;
      height: 360px;
      background: transparent;
      border: none;
      padding: 0;
      perspective: 1400px;
    }

    .ab-team-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      cursor: pointer;
      transform-style: preserve-3d;
      transition: transform 0.7s cubic-bezier(0.45, 0.05, 0.2, 1);
      outline: none;
    }

    /* Desktop / mouse: flip on hover */
    @media (hover: hover) {
      .ab-team-card:hover .ab-team-card-inner { transform: rotateY(180deg); }
    }
    /* Touch / keyboard: flip on tap or focus */
    .ab-team-card-inner.is-flipped,
    .ab-team-card-inner:focus-visible { transform: rotateY(180deg); }

    .ab-team-card-face {
      position: absolute;
      inset: 0;
      overflow: hidden;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      display: flex;
      flex-direction: column;
      transition: border-color 0.25s ease;
    }
    .ab-team-card-inner:hover .ab-team-card-face { border-color: var(--rose-accent); }

    .ab-team-card-back {
      transform: rotateY(180deg);
      background-color: var(--card-cream);
      justify-content: space-between;
    }

    .ab-team-photo {
      height: 190px;
      flex-shrink: 0;
      background-color: var(--card-cream);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 2.4rem;
      font-weight: 700;
      color: var(--rose-accent);
      border-bottom: 1px solid var(--border-hairline);
    }

    .ab-team-body { padding: 22px 24px 26px; }

    .ab-team-name {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 4px;
    }

    .ab-team-role {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--rose-accent);
      margin-bottom: 12px;
    }


    /* Back face content */
    .ab-team-back-body {
      padding: 26px 24px;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .ab-team-back-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-weight: 700;
      font-size: 0.95rem;
      color: var(--rose-accent);
      margin-bottom: 14px;
      flex-shrink: 0;
    }
    .ab-team-back-body .ab-team-name { font-size: 1.05rem; }
    .ab-team-back-bio {
      font-size: 0.85rem;
      color: #9C9284;
      line-height: 1.6;
      flex-grow: 1;
    }
    .ab-team-back-cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-top: 16px;
      padding: 10px 18px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background-color: var(--rose-accent);
      color: #15130E;
      border-radius: 4px;
      transition: background-color 0.2s ease;
      flex-shrink: 0;
    }
    .ab-team-back-cta:hover { background-color: var(--rose-accent-hover); }
    .ab-team-flip-hint {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--bronze-taupe);
      padding: 0 24px 16px;
      flex-shrink: 0;
    }

    /* ---------- 6. Visit — split band ---------- */
    .ab-visit {
      display: grid;
      grid-template-columns: 1fr;
      border-top: 1px solid var(--border-hairline);
    }

    @media (min-width: 860px) {
      .ab-visit { grid-template-columns: 1fr 1fr; min-height: 380px; }
    }

    .ab-visit-info {
      background-color: var(--card-cream);
      padding: 64px clamp(24px, 6vw, 72px);
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .ab-visit-info .hours-pin {
      width: 46px; height: 46px;
      border-radius: 50%;
      border: 1px solid var(--rose-accent);
      display: flex; align-items: center; justify-content: center;
      color: var(--rose-accent);
    }

    .ab-visit-info h3 {
      font-size: clamp(1.6rem, 3vw, 2.1rem);
      font-weight: 700;
      color: var(--text-ink);
      margin: 16px 0 12px;
    }

    .ab-visit-info p { font-size: 0.95rem; color: #B3AA98; }

    .ab-visit-image {
      min-height: 280px;
      background: url('https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=1200&q=70&auto=format&fit=crop') center / cover no-repeat;
      filter: grayscale(10%) contrast(1.05);
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
          <li><a href="reviews.php" class="nav-link">Reviews</a></li>
          <li><a href="about.php" class="nav-link active" aria-current="page"><span class="nav-underline" aria-hidden="true"></span>About</a></li>
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

    <!-- 1. Cinematic Hero -->
    <section class="ab-hero">
      <div class="ab-hero-bg" aria-hidden="true"></div>
      <div class="ab-hero-scrim" aria-hidden="true"></div>
      <div class="container">
        <div class="ab-hero-inner reveal">
          <h1 class="font-serif">Twelve Years of<br>Professional Grooming</h1>
          <p>A trusted salon offering expert haircuts, grooming and bridal makeup, delivered with skilled craftsmanship, strict hygiene and transparent pricing.</p>
          <div class="ab-hero-actions">
            <a href="#ab-story" class="ab-scroll-cue">Our Story &darr;</a>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Stat Strip -->
    <section class="ab-stat-strip reveal">
      <div class="container ab-stat-strip-inner">
        <div class="ab-stat">
          <span>12+</span>
          <span class="ab-stat-label">Years Serving Ghodasar</span>
        </div>
        <div class="ab-stat">
          <span>4</span>
          <span class="ab-stat-label">Expert Stylists &amp; Artists</span>
        </div>
        <div class="ab-stat">
          <span>&#8377;100</span>
          <span class="ab-stat-label">Haircuts Starting At</span>
        </div>
        <div class="ab-stat">
          <span>7/7</span>
          <span class="ab-stat-label">Days a Week, 9 AM &ndash; 9 PM</span>
        </div>
      </div>
    </section>

    <div class="container">

      <!-- 3. Story Timeline -->
      <section class="ab-section reveal" id="ab-story">
        <div class="category-header">
          <span class="eyebrow-tag">OUR JOURNEY</span>
          <div class="hairline"></div>
        </div>
        <h2 class="font-serif ab-section-title">How Deepkala Came to Be</h2>

        <div class="ab-timeline">
          <div class="ab-timeline-item">
            <div class="ab-timeline-marker">01</div>
            <div class="ab-timeline-content">
              <h3 class="font-serif">Our Mission</h3>
              <p>Deepkala Hair Salon was established with a clear purpose: to provide every client with an expert haircut, precise beard grooming and restorative hair therapy at transparent, fair prices. We believe looking well-groomed and feeling confident should be part of everyday life, not reserved for special occasions.</p>
            </div>
          </div>
          <div class="ab-timeline-item">
            <div class="ab-timeline-marker">02</div>
            <div class="ab-timeline-content">
              <h3 class="font-serif">Our Craft</h3>
              <p>Our team combines traditional barbering with contemporary styling techniques. Whether it is a skin fade, a classic scissor cut, a relaxing hair spa or a hot towel beard shave, every service receives the same careful attention to detail.</p>
            </div>
          </div>
          <div class="ab-timeline-item">
            <div class="ab-timeline-marker">03</div>
            <div class="ab-timeline-content">
              <h3 class="font-serif">Our Clients</h3>
              <p>We have built lasting relationships with families and working professionals over the years. Every appointment is designed to be clean, comfortable and refreshing, and that is the standard we uphold every day.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 4. Values — numbered list -->
      <section class="ab-section reveal">
        <div class="category-header">
          <span class="eyebrow-tag">OUR VALUES</span>
          <div class="hairline"></div>
        </div>
        <h2 class="font-serif ab-section-title">What We Stand For</h2>

        <div class="ab-values-list">
          <div class="ab-value-row">
            <div class="ab-value-num">01</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Master Craftsmanship</h3>
              <p>Every haircut is planned around the client's head shape, hair growth pattern and personal style before the first cut.</p>
            </div>
          </div>
          <div class="ab-value-row">
            <div class="ab-value-num">02</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Hygiene First</h3>
              <p>All scissors, razors, clippers and capes are sanitised before every client, without exception.</p>
            </div>
          </div>
          <div class="ab-value-row">
            <div class="ab-value-num">03</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Transparent Pricing</h3>
              <p>Haircuts start at &#8377;100, with clear pricing on every service and no hidden charges.</p>
            </div>
          </div>
          <div class="ab-value-row">
            <div class="ab-value-num">04</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Comfort &amp; Service</h3>
              <p>Open seven days a week, 9:00 AM to 9:00 PM, with comfortable seating, punctual service and courteous staff.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 5. Team filmstrip -->
      <section class="ab-section">
        <div class="category-header">
          <span class="eyebrow-tag">OUR TEAM</span>
          <div class="hairline"></div>
        </div>
        <h2 class="font-serif ab-section-title">Meet Our Experts</h2>

        <div class="ab-team-scroll">

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Deepesh Parmar" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">DP</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Deepesh Parmar</div>
                  <div class="ab-team-role">Founder &amp; Master Stylist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">DP</div>
                  <div class="ab-team-name">Deepesh Parmar</div>
                  <div class="ab-team-role">Founder &amp; Master Stylist</div>
                  <p class="ab-team-back-bio">Over 12 years of experience in classic barbering, advanced scissor techniques and client consultation.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Kalpesh Patel" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">KP</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Kalpesh Patel</div>
                  <div class="ab-team-role">Senior Fade &amp; Colour Specialist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">KP</div>
                  <div class="ab-team-name">Kalpesh Patel</div>
                  <div class="ab-team-role">Senior Fade &amp; Colour Specialist</div>
                  <p class="ab-team-back-bio">Specialises in skin fades, undercuts, textured crop cuts and custom hair colouring.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Sanjay Kumar" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">SK</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Sanjay Kumar</div>
                  <div class="ab-team-role">Beard Specialist &amp; Spa Therapist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">SK</div>
                  <div class="ab-team-name">Sanjay Kumar</div>
                  <div class="ab-team-role">Beard Specialist &amp; Spa Therapist</div>
                  <p class="ab-team-back-bio">Experienced in razor lining, hot towel shaves, scalp massage and nourishing hair spa treatments.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Nisha Solanki" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <!-- TODO: placeholder name — replace with the artist's real name, photo and experience -->
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">NS</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Nisha Solanki</div>
                  <div class="ab-team-role">Bridal &amp; Wedding Makeup Artist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">NS</div>
                  <div class="ab-team-name">Nisha Solanki</div>
                  <div class="ab-team-role">Bridal &amp; Wedding Makeup Artist</div>
                  <p class="ab-team-back-bio">Specialises in bridal, engagement and reception makeup, tailored to the occasion, outfit and skin type.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>


          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Rahul Desai" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">RD</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Rahul Desai</div>
                  <div class="ab-team-role">Senior Barber</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">RD</div>
                  <div class="ab-team-name">Rahul Desai</div>
                  <div class="ab-team-role">Senior Barber</div>
                  <p class="ab-team-back-bio">Skin fades, classic tapers and sharp hairlines, built on years behind the chair.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Amit Joshi" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">AJ</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Amit Joshi</div>
                  <div class="ab-team-role">Beard &amp; Grooming Specialist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">AJ</div>
                  <div class="ab-team-name">Amit Joshi</div>
                  <div class="ab-team-role">Beard &amp; Grooming Specialist</div>
                  <p class="ab-team-back-bio">Beard shaping, hot-towel shaves and detailed grooming for a clean, polished finish.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Priya Shah" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">PS</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Priya Shah</div>
                  <div class="ab-team-role">Hair Colour Specialist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">PS</div>
                  <div class="ab-team-name">Priya Shah</div>
                  <div class="ab-team-role">Hair Colour Specialist</div>
                  <p class="ab-team-back-bio">Global colour, highlights and toning, planned around your hair type and skin tone.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Vikram Rathod" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">VR</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Vikram Rathod</div>
                  <div class="ab-team-role">Stylist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">VR</div>
                  <div class="ab-team-name">Vikram Rathod</div>
                  <div class="ab-team-role">Stylist</div>
                  <p class="ab-team-back-bio">Modern cuts and textured styling, with a calm and precise approach to every client.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

    </div>

    <!-- 6. Visit — split band -->
    <section class="ab-visit reveal">
      <div class="ab-visit-info">
        <div class="hours-pin"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <h3 class="font-serif">Visit Our Salon</h3>
        <p>2 Jayvimal Society, Cadila Road, Ghodasar, Ahmedabad &ndash; 380050</p>
        <p style="font-weight: 600; color: var(--rose-accent); margin-top: 6px;">Open Daily, 9:00 AM &ndash; 9:00 PM</p>
        <div>
          <a href="booking.php" class="btn-primary">Book an Appointment</a>
        </div>
      </div>
      <div class="ab-visit-image" aria-hidden="true"></div>
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

  <!-- Floating WhatsApp -->
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
