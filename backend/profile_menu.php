<?php if (!defined('DK_PROFILE_MENU_ASSETS')) { define('DK_PROFILE_MENU_ASSETS', true); ?>
<style>
  .dk-profile-wrap { position: relative; }

  .dk-profile-btn { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; padding: 0; color: var(--text-ink, #FFFFFF); border: 1px solid var(--border-hairline, #7A6C55); background: transparent; border-radius: 50%; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }

  .dk-profile-btn:hover { border-color: var(--rose-accent, #C9A66B); color: var(--rose-accent, #C9A66B); }

  .dk-profile-menu { display: none; position: absolute; top: calc(100% + 10px); right: 0; background: var(--card-cream, #221E17); border: 1px solid var(--border-hairline, #7A6C55); border-radius: 8px; min-width: 210px; box-shadow: 0 12px 30px rgba(0,0,0,0.25); z-index: 200; overflow: hidden; }

  .dk-profile-menu.open { display: block; }

  .dk-profile-menu-head { padding: 12px 16px; font-size: 0.75rem; color: var(--bronze-taupe, #A9884C); border-bottom: 1px solid var(--border-hairline, #7A6C55); line-height: 1.5; }

  .dk-profile-menu-head strong { color: var(--text-ink, #FFFFFF); font-size: 0.85rem; }

  .dk-profile-menu a { display: block; padding: 11px 16px; font-size: 0.82rem; color: var(--text-ink, #FFFFFF); text-decoration: none; border-bottom: 1px solid var(--border-hairline, #7A6C55); }

  .dk-profile-menu a:last-child { border-bottom: none; }

  .dk-profile-menu a:hover { background: rgba(169,136,76,0.08); }

  .dk-profile-menu a.dk-profile-logout { color: var(--rose-accent, #C9A66B); font-weight: 600; }
</style>
<script>
  function dkToggleProfileMenu(e) {
    e.stopPropagation();
    document.getElementById('dkProfileMenu').classList.toggle('open');
  }
  document.addEventListener('click', function (e) {
    var menu = document.getElementById('dkProfileMenu');
    if (menu && !e.target.closest('.dk-profile-wrap')) menu.classList.remove('open');
  });
</script>
<?php } ?>
<?php $dkIconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'; ?>
<?php if (!empty($_SESSION['user_id'])) { ?>
  <div class="dk-profile-wrap">
    <button type="button" class="dk-profile-btn" onclick="dkToggleProfileMenu(event)" aria-label="My profile" title="My profile"><?php echo $dkIconSvg; ?></button>
    <div class="dk-profile-menu" id="dkProfileMenu">
      <div class="dk-profile-menu-head">Signed in as<br><strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></div>
      <a href="profile.php">My Profile</a>
      <a href="logout.php" class="dk-profile-logout">Logout</a>
    </div>
  </div>
<?php } else { ?>
  <a href="login.php?redirect=<?php echo urlencode(basename($_SERVER['PHP_SELF'])); ?>" class="dk-profile-btn" aria-label="Login" title="Login"><?php echo $dkIconSvg; ?></a>
<?php } ?>
