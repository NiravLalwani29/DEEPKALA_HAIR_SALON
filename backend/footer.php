<?php
$subEmail = strtolower($_SESSION['user_email'] ?? '');
$subbed = false;
if ($subEmail !== '' && isset($conn)) {
    try { $subbed = (bool) row(q($conn, "SELECT 1 FROM subscribers WHERE email = ?", $subEmail)); } catch (Throwable $e) {}
}
?>
  <footer class="dk-footer" id="site-footer">
    <div class="dk-ft-wrap">
      <div class="dk-ft-top">

        <div class="dk-ft-col dk-ft-about">
          <a href="index.php" class="dk-ft-logo" aria-label="Deepkala Hair Salon">
            <img src="assets/deepkala-emblem.png" width="34" height="34" alt="">
            <span>Deepkala</span>
          </a>
          <p class="dk-ft-tagline">Premium Hair &amp; Grooming Studio</p>
          <h3 class="dk-ft-h dk-ft-h-contact">Contact Us</h3>
          <ul class="dk-ft-contact">
            <li>
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 006.6 6.6l2.2-2.2a1 1 0 011-.25 11.4 11.4 0 003.6.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1z"/></svg>
              <a href="tel:+919924620958">+91 99246 20958</a>
            </li>
            <li>
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18a1 1 0 011 1v.4l-10 6.3L2 6.4V6a1 1 0 011-1zm-1 4l10 6.3L22 9v9a1 1 0 01-1 1H3a1 1 0 01-1-1V9z"/></svg>
              <a href="mailto:deepkalahairsalon@gmail.com">deepkalahairsalon@gmail.com</a>
            </li>
          </ul>
        </div>

        <nav class="dk-ft-col dk-ft-links" aria-label="Quick Links">
          <h3 class="dk-ft-h">Quick Links</h3>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="reviews.php">Client Reviews</a></li>
            <li><a href="about.php">About</a></li>
          </ul>
        </nav>

        <nav class="dk-ft-col dk-ft-links dk-ft-helpful" aria-label="Book and Visit">
          <h3 class="dk-ft-h">Book &amp; Visit</h3>
          <ul>
            <li><a href="booking.php">Reserve Slot</a></li>
            <li><a href="membership.php">Membership</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </nav>

        <div class="dk-ft-col dk-ft-links dk-ft-salon">
          <h3 class="dk-ft-h">Salon Info</h3>
          <ul class="dk-ft-salon-list">
            <li>
              <span class="dk-ft-ico"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 19.5s-6.5-5.4-6.5-10.5a6.5 6.5 0 0113 0c0 5.1-6.5 10.5-6.5 10.5zM12 6.5a2.5 2.5 0 100 5 2.5 2.5 0 000-5z"/><ellipse cx="12" cy="21.6" rx="5" ry="1.1" opacity=".55"/></svg></span>
              <div><a href="https://www.google.com/maps/search/?api=1&amp;query=2+Jayvimal+Society+Cadila+Road+Ghodasar+Ahmedabad+380050" target="_blank" rel="noopener noreferrer" title="Open in Google Maps">2 Jayvimal Society, Cadila Road, Ghodasar, Ahmedabad &ndash; 380050</a></div>
            </li>
            <li>
              <span class="dk-ft-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
              <div>9:00 AM &ndash; 9:00 PM, Daily</div>
            </li>
          </ul>
        </div>

        <div class="dk-ft-col dk-ft-sub">
          <h3 class="dk-ft-sub-title">Subscribe More Info</h3>
          <form class="dk-ft-form">
            <label class="dk-ft-input">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18a1 1 0 011 1v.4l-10 6.3L2 6.4V6a1 1 0 011-1zm-1 4l10 6.3L22 9v9a1 1 0 01-1 1H3a1 1 0 01-1-1V9z"/></svg>
              <input type="email" name="email" placeholder="Enter your Email" aria-label="Email address" autocomplete="email" value="<?= htmlspecialchars($subEmail) ?>" required<?= $subbed ? ' readonly' : '' ?>>
            </label>
            <button type="submit"<?= $subbed ? ' disabled' : '' ?>><?= $subbed ? 'Subscribed' : 'Subscribe' ?></button>
          </form>
        </div>

        <a href="#" class="dk-ft-top-btn" aria-label="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 15l7-7 7 7" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>

      <hr class="dk-ft-rule">

      <div class="dk-ft-bottom">
        <div class="dk-ft-social">
          <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8V6.5c0-.7.3-1 1-1h2V2h-3a4 4 0 00-4 4v2H7.5v3.5H10V22h4v-10.5h2.6L17 8z"/></svg></a>
          <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M.057 24l1.687-6.163A11.87 11.87 0 01.157 11.89C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 018.413 3.488 11.82 11.82 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807a9.87 9.87 0 005.392 1.592c5.448 0 9.886-4.434 9.889-9.885a9.86 9.86 0 00-9.881-9.892c-5.452 0-9.887 4.434-9.889 9.884 0 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></a>
          <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></svg></a>
          <a href="https://www.google.com/maps/search/?api=1&amp;query=2+Jayvimal+Society+Cadila+Road+Ghodasar+Ahmedabad+380050" target="_blank" rel="noopener noreferrer" aria-label="Find us on Google Maps"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 22s-7-6-7-11.5a7 7 0 0114 0C19 16 12 22 12 22zm0-14a2.7 2.7 0 100 5.4A2.7 2.7 0 0012 8z"/></svg></a>
        </div>
        <p class="dk-ft-copy">&copy; 2026 Deepkala Hair Salon. All rights reserved.</p>
      </div>
    </div>
  </footer>
<script src="assets/footer.js" defer></script>
