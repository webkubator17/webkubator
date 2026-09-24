(() => {
  document.documentElement.classList.add('js');

  const header = document.querySelector('[data-header]');
  const menu = document.querySelector('.menu-toggle');
  const menuClose = document.querySelector('.menu-close');
  const nav = document.querySelector('#site-nav');
  document.querySelectorAll('.contact-icon').forEach((element) => element.setAttribute('aria-hidden', 'true'));

  const updateHeader = () => header?.classList.toggle('scrolled', window.scrollY > 18);
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  const closeMenu = () => {
    if (!menu || !nav) return;
    menu.setAttribute('aria-expanded', 'false');
    menu.setAttribute('aria-label', 'Buka menu navigasi');
    nav.classList.remove('is-open');
  };

  menu?.addEventListener('click', () => {
    const open = menu.getAttribute('aria-expanded') === 'true';
    menu.setAttribute('aria-expanded', String(!open));
    menu.setAttribute('aria-label', open ? 'Buka menu navigasi' : 'Tutup menu navigasi');
    nav?.classList.toggle('is-open', !open);
  });
  menuClose?.addEventListener('click', closeMenu);
  nav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(); });

  const revealItems = document.querySelectorAll('.reveal');
  const counterItems = document.querySelectorAll('[data-counter]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const showCounter = (element) => {
    const target = Number(element.dataset.counter || 0);
    if (reducedMotion) {
      element.textContent = String(target);
      return;
    }
    const duration = 900;
    const start = performance.now();
    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = String(Math.round(target * eased));
      if (progress < 1) window.requestAnimationFrame(tick);
    };
    window.requestAnimationFrame(tick);
  };

  if ('IntersectionObserver' in window && !reducedMotion) {
    const observer = new IntersectionObserver((entries, instance) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        if (entry.target.matches('[data-counter]')) showCounter(entry.target);
        instance.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px' });
    revealItems.forEach((element) => observer.observe(element));
    counterItems.forEach((element) => observer.observe(element));
  } else {
    revealItems.forEach((element) => element.classList.add('is-visible'));
    counterItems.forEach(showCounter);
  }

  const setupLogoMarquee = () => {
    const marquee = document.querySelector('[data-logo-marquee]');
    const track = marquee?.querySelector('[data-logo-marquee-track]');
    const group = track?.querySelector('.logo-marquee-group');
    if (!marquee || !track || !group) return;

    const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    const defaultSpeed = 42;
    let loopWidth = 0;
    let offset = 0;
    let previousTime = performance.now();
    let pointerId = null;
    let lastPointerX = 0;
    let dragging = false;

    const normalizeOffset = () => {
      if (!loopWidth) return;
      offset %= loopWidth;
      if (offset < 0) offset += loopWidth;
    };

    const measure = () => {
      loopWidth = group.getBoundingClientRect().width;
      normalizeOffset();
      track.style.transform = `translate3d(${-offset}px, 0, 0)`;
    };

    const startDrag = (event) => {
      if (event.pointerType === 'mouse' && event.button !== 0) return;
      dragging = true;
      pointerId = event.pointerId;
      lastPointerX = event.clientX;
      track.classList.add('is-dragging');
      track.setPointerCapture?.(event.pointerId);
    };

    const moveDrag = (event) => {
      if (!dragging || event.pointerId !== pointerId) return;
      const deltaX = event.clientX - lastPointerX;
      lastPointerX = event.clientX;
      offset -= deltaX;
      normalizeOffset();
      track.style.transform = `translate3d(${-offset}px, 0, 0)`;
    };

    const stopDrag = (event) => {
      if (!dragging || (event?.pointerId != null && event.pointerId !== pointerId)) return;
      dragging = false;
      track.classList.remove('is-dragging');
      if (pointerId != null) track.releasePointerCapture?.(pointerId);
      pointerId = null;
    };

    track.addEventListener('pointerdown', startDrag);
    track.addEventListener('pointermove', moveDrag);
    track.addEventListener('pointerup', stopDrag);
    track.addEventListener('pointercancel', stopDrag);
    track.addEventListener('lostpointercapture', stopDrag);
    track.addEventListener('dragstart', (event) => event.preventDefault());
    measure();
    window.addEventListener('resize', measure, { passive: true });

    const tick = (now) => {
      const deltaTime = Math.min((now - previousTime) / 1000, 0.05);
      previousTime = now;
      if (!dragging && !motionQuery.matches) {
        offset += defaultSpeed * deltaTime;
        normalizeOffset();
        track.style.transform = `translate3d(${-offset}px, 0, 0)`;
      }
      window.requestAnimationFrame(tick);
    };
    window.requestAnimationFrame(tick);
  };

  const setupPricingSelector = () => {
    const options = [...document.querySelectorAll('[data-pricing-option]')];
    const grid = document.querySelector('[data-pricing-grid]');
    const dataElement = document.querySelector('#pricing-catalog');
    if (!options.length || !grid || !dataElement) return;

    let catalog;
    try {
      catalog = JSON.parse(dataElement.textContent || '{}');
    } catch {
      return;
    }

    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[character]));
    const iconForFeature = (feature) => {
      const value = String(feature).toLowerCase();
      const icons = {
        domain: 'fi-rr-globe', halaman: 'fi-rr-browser', produk: 'fi-rr-box-open', hosting: 'fi-rr-database',
        ssl: 'fi-rr-shield-check', bandwidth: 'fi-rr-chart-line-up', email: 'fi-rr-envelope', sosial: 'fi-rr-share',
        whatsapp: 'fi-rr-paper-plane', manual: 'fi-rr-book-alt', kontak: 'fi-rr-form', template: 'fi-rr-palette',
        seo: 'fi-rr-search', plugin: 'fi-rr-puzzle-piece', garansi: 'fi-rr-badge-check', brand: 'fi-rr-star',
        tombol: 'fi-rr-link', mobile: 'fi-rr-mobile', katalog: 'fi-rr-list-check', keranjang: 'fi-rr-shopping-cart',
        checkout: 'fi-rr-credit-card', pesanan: 'fi-rr-receipt', pembayaran: 'fi-rr-wallet', stok: 'fi-rr-boxes',
        voucher: 'fi-rr-ticket', laporan: 'fi-rr-chart-histogram', profil: 'fi-rr-id-badge', organisasi: 'fi-rr-users',
        maps: 'fi-rr-marker', publik: 'fi-rr-document', berita: 'fi-rr-newspaper', program: 'fi-rr-calendar',
        analytics: 'fi-rr-chart-line-up', form: 'fi-rr-form'
      };
      return Object.entries(icons).find(([keyword]) => value.includes(keyword))?.[1] || 'fi-rr-check';
    };
    const detailLink = (key, plan) => `/pricing/?category=${encodeURIComponent(key)}&plan=${encodeURIComponent(plan.name)}`;
    const featureList = (features) => {
      const item = (feature) => `<li><i class="fi ${iconForFeature(feature)}" aria-hidden="true"></i><span>${escapeHtml(feature)}</span></li>`;
      const visible = features.slice(0, 5).map(item).join('');
      const extra = features.slice(5).map(item).join('');
      return `${visible}${extra ? `<li class="price-more-wrap"><details class="price-more"><summary><i class="fi fi-rr-plus" aria-hidden="true"></i><span>Lihat lebih banyak</span></summary><ul>${extra}</ul></details></li>` : ''}`;
    };

    const render = (key) => {
      const category = catalog[key] || catalog['landing-page'];
      if (!category) return;
      options.forEach((option) => {
        const active = option.dataset.pricingOption === key;
        option.classList.toggle('is-active', active);
        option.setAttribute('aria-pressed', String(active));
      });
      grid.innerHTML = category.plans.map((plan, index) => `<article class="price-card ${index === 1 ? 'price-card-featured' : ''} is-visible"><div class="price-header"><h3>${escapeHtml(plan.name)}</h3>${index === 1 ? '<span class="price-badge">Pilihan terbaik</span>' : ''}</div><strong class="price">Rp${escapeHtml(plan.price)}</strong><p class="renewal">${escapeHtml(plan.renewal)}</p><ul>${featureList(plan.features)}</ul><a class="button ${index === 1 ? 'button-primary' : 'button-outline'}" href="${detailLink(key, plan)}">Pilih Paket <i class="fi fi-rr-arrow-right" aria-hidden="true"></i></a></article>`).join('');
    };

    options.forEach((option) => option.addEventListener('click', () => render(option.dataset.pricingOption)));
    const queryKey = new URLSearchParams(window.location.search).get('pricing');
    if (queryKey && catalog[queryKey]) render(queryKey);
  };

  setupLogoMarquee();
  setupPricingSelector();
})();
