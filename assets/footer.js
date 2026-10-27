document.addEventListener('submit', async (e) => {
  const f = e.target.closest('.dk-ft-form');
  if (!f) return;
  e.preventDefault();
  const b = f.querySelector('button'), label = b.textContent;
  b.disabled = true;
  b.textContent = 'Sending…';
  try {
    const d = await (await fetch('backend/subscribe.php', { method: 'POST', body: new FormData(f) })).json();
    if (d.login) return location.href = 'login.php?redirect=' + location.pathname.split('/').pop();
    b.textContent = d.msg;
    if (d.ok) return f.email.readOnly = true;
  } catch {
    b.textContent = 'Try again';
  }
  setTimeout(() => { b.textContent = label; b.disabled = false; }, 2500);
});
