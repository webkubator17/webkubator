(() => {
  const page = document.querySelector('[data-pricing-detail]');
  if (!page) return;

  const formatRupiah = (value) => `Rp${new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(value)))}`;
  const basePrice = Number(page.dataset.basePrice || 0);
  const renewalPrice = Number(page.dataset.renewalPrice || 0);
  const category = document.querySelector('.summary-category')?.textContent.trim() || 'Website';
  const plan = document.querySelector('#summary-title')?.textContent.trim() || 'Paket';
  const freeDomain = page.dataset.freeDomain || '.COM';
  const waNumber = page.dataset.waNumber || '6287753719307';
  const durationButtons = [...document.querySelectorAll('[data-duration]')];
  const domainInput = document.querySelector('#domain-input');
  const domainCheckButton = document.querySelector('#domain-check');
  const domainStatus = document.querySelector('#domain-status');
  const domainStatusIcon = domainStatus?.querySelector('i');
  const domainStatusText = domainStatus?.querySelector('span');
  const orderLink = document.querySelector('#order-whatsapp');
  const summaryDuration = document.querySelector('#summary-duration');
  const summaryService = document.querySelector('#summary-service');
  const summaryDiscountRow = document.querySelector('#summary-discount-row');
  const summaryDiscount = document.querySelector('#summary-discount');
  const summaryTotal = document.querySelector('#summary-total');
  const summaryDomain = document.querySelector('#summary-domain');
  let years = 1;
  let checkedDomain = '';
  let domainState = 'empty';
  let checkTimer = null;
  let activeController = null;

  const setStatus = (type, message, icon = 'fi-rr-info') => {
    if (!domainStatus || !domainStatusText || !domainStatusIcon) return;
    domainStatus.className = `domain-status${type ? ` is-${type}` : ''}`;
    domainStatusText.textContent = message;
    domainStatusIcon.className = `fi ${icon}`;
  };

  const normalizeDomain = () => {
    const raw = String(domainInput?.value || '').trim().toLowerCase()
      .replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0].replace(/\.$/, '');
    if (domainInput) domainInput.value = raw;
    return raw;
  };

  const isValidDomain = (domain) => /^(?=.{4,253}$)([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i.test(domain);

  const updateOrderLink = () => {
    const subtotal = basePrice + (renewalPrice * (years - 1));
    const discountRate = years === 2 ? 0.2 : years === 3 ? 0.3 : 0;
    const discount = Math.round(subtotal * discountRate);
    const total = subtotal - discount;
    const domain = checkedDomain || 'belum diisi';
    const message = [
      'Halo Webkubator, saya ingin memesan paket website.',
      `Jenis website: ${category}`,
      `Paket: ${plan}`,
      `Durasi: ${years} tahun`,
      `Domain: ${domain}`,
      `Free domain: ${freeDomain} (1 tahun)`,
      'Free hosting: termasuk sesuai paket',
      `Harga jasa: ${formatRupiah(subtotal)}`,
      `Diskon durasi: ${formatRupiah(discount)}`,
      `Total: ${formatRupiah(total)}`,
      'Mohon bantu proses dan konfirmasi detailnya.'
    ].join('\n');
    if (orderLink) {
      orderLink.href = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;
      const enabled = domainState === 'available' && Boolean(checkedDomain);
      orderLink.classList.toggle('is-disabled', !enabled);
      orderLink.setAttribute('aria-disabled', String(!enabled));
    }
  };

  const updateSummary = () => {
    const subtotal = basePrice + (renewalPrice * (years - 1));
    const discountRate = years === 2 ? 0.2 : years === 3 ? 0.3 : 0;
    const discount = Math.round(subtotal * discountRate);
    const total = subtotal - discount;
    if (summaryDuration) summaryDuration.textContent = `${years} tahun`;
    if (summaryService) summaryService.textContent = formatRupiah(subtotal);
    if (summaryDiscountRow) summaryDiscountRow.hidden = discount === 0;
    if (summaryDiscount) summaryDiscount.textContent = `-${formatRupiah(discount)}`;
    if (summaryTotal) summaryTotal.textContent = formatRupiah(total);
    if (summaryDomain) summaryDomain.textContent = checkedDomain || 'Belum diisi';
    updateOrderLink();
  };

  const checkDomain = async () => {
    const domain = normalizeDomain();
    checkedDomain = '';
    domainState = 'empty';
    updateSummary();
    if (!domain) {
      setStatus('', 'Isi domain untuk mulai memeriksa.');
      return;
    }
    if (!isValidDomain(domain)) {
      domainState = 'invalid';
      setStatus('error', 'Format domain belum benar. Tambahkan ekstensi domain yang valid.', 'fi-rr-cross-circle');
      return;
    }
    activeController?.abort();
    activeController = new AbortController();
    if (domainCheckButton) domainCheckButton.disabled = true;
    setStatus('', 'Sedang memeriksa ketersediaan domain...', 'fi-rr-refresh');
    try {
      const response = await fetch(`https://rdap.org/domain/${encodeURIComponent(domain)}`, {
        headers: { Accept: 'application/rdap+json' },
        cache: 'no-store',
        signal: activeController.signal
      });
      if (response.status === 404) {
        checkedDomain = domain;
        domainState = 'available';
        setStatus('success', `${domain} tersedia dan siap diajukan.`, 'fi-rr-check-circle');
      } else if (response.ok) {
        domainState = 'used';
        setStatus('error', `${domain} sudah digunakan. Silakan pilih nama domain lain.`, 'fi-rr-cross-circle');
      } else {
        domainState = 'unknown';
        setStatus('warning', 'Status domain belum dapat dipastikan. Coba periksa lagi.', 'fi-rr-exclamation');
      }
    } catch (error) {
      if (error.name !== 'AbortError') {
        domainState = 'unknown';
        setStatus('warning', 'Pemeriksaan otomatis sedang tidak tersedia. Coba lagi sebentar.', 'fi-rr-exclamation');
      }
    } finally {
      if (domainCheckButton) domainCheckButton.disabled = false;
      updateSummary();
    }
  };

  durationButtons.forEach((button) => {
    button.addEventListener('click', () => {
      years = Number(button.dataset.duration || 1);
      durationButtons.forEach((item) => {
        const active = item === button;
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-pressed', String(active));
      });
      updateSummary();
    });
  });

  domainInput?.addEventListener('input', () => {
    checkedDomain = '';
    domainState = 'empty';
    clearTimeout(checkTimer);
    setStatus('', 'Domain akan diperiksa otomatis setelah Anda selesai mengetik.');
    updateSummary();
    const domain = normalizeDomain();
    if (isValidDomain(domain)) checkTimer = window.setTimeout(checkDomain, 700);
  });
  domainInput?.addEventListener('blur', checkDomain);
  domainCheckButton?.addEventListener('click', checkDomain);
  orderLink?.addEventListener('click', (event) => {
    if (orderLink.getAttribute('aria-disabled') === 'true') {
      event.preventDefault();
      domainInput?.focus();
      setStatus('warning', 'Periksa domain yang tersedia terlebih dahulu.', 'fi-rr-exclamation');
    }
  });

  updateSummary();
})();
