(() => {
  const links = document.querySelectorAll('.portal-link');
  const menuToggle = document.querySelector('.portal-menu-toggle');
  const menu = document.querySelector('#portal-site-nav');
  const menuClose = document.querySelector('.portal-menu-close');
  const backdrop = document.querySelector('.portal-menu-backdrop');

  const closeMenu = () => {
    if (!menuToggle || !menu) return;
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.setAttribute('aria-label', 'Buka menu navigasi');
    menu.classList.remove('is-open');
    backdrop?.classList.remove('is-visible');
    document.body.classList.remove('portal-menu-open');
  };

  const openMenu = () => {
    if (!menuToggle || !menu) return;
    menuToggle.setAttribute('aria-expanded', 'true');
    menuToggle.setAttribute('aria-label', 'Tutup menu navigasi');
    menu.classList.add('is-open');
    backdrop?.classList.add('is-visible');
    document.body.classList.add('portal-menu-open');
    menu.querySelector('a')?.focus();
  };

  menuToggle?.addEventListener('click', () => {
    menuToggle.getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu();
  });
  menuClose?.addEventListener('click', closeMenu);
  backdrop?.addEventListener('click', closeMenu);
  menu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMenu();
  });

  links.forEach((link) => {
    link.addEventListener('pointerdown', () => link.classList.add('is-pressed'), { passive: true });
    link.addEventListener('pointerup', () => link.classList.remove('is-pressed'), { passive: true });
    link.addEventListener('pointercancel', () => link.classList.remove('is-pressed'), { passive: true });
  });
})();
