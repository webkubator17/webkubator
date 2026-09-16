(() => {
  const links = document.querySelectorAll('.portal-link');
  if (!links.length) return;

  links.forEach((link) => {
    link.addEventListener('pointerdown', () => link.classList.add('is-pressed'), { passive: true });
    link.addEventListener('pointerup', () => link.classList.remove('is-pressed'), { passive: true });
    link.addEventListener('pointercancel', () => link.classList.remove('is-pressed'), { passive: true });
  });
})();

