(() => {
  const page = document.querySelector('[data-pricing-detail]');
  if (!page) return;

  const formatRupiah = (value) => `Rp${new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(value)))}`;
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
  const basePrice = Number(page.dataset.basePrice || 0);
  const renewalPrice = Number(page.dataset.renewalPrice || 0);
  const category = page.dataset.categoryLabel || 'Website';
  const plan = page.dataset.planName || 'Paket';
  const freeDomain = String(page.dataset.freeDomain || '.COM').toLowerCase();
  const waNumber = page.dataset.waNumber || '6287753719307';
  const domainCatalog = [
    { extension: '.com', label: '.COM', price: 180000 },
    { extension: '.web.id', label: '.WEB.ID', price: 80000 },
    { extension: '.id', label: '.ID', price: 250000 }
  ];
  const freeExtension = domainCatalog.find((item) => freeDomain.includes(item.extension))?.extension || '.com';
  const durationButtons = [...document.querySelectorAll('[data-duration]')];
  const domainInput = document.querySelector('#domain-input');
  const domainCheckButton = document.querySelector('#domain-check');
  const domainStatus = document.querySelector('#domain-status');
  const domainStatusIcon = domainStatus?.querySelector('i');
  const domainStatusText = domainStatus?.querySelector('span');
  const domainOptions = document.querySelector('#domain-options');
  const orderLink = document.querySelector('#order-whatsapp');
  const summaryDuration = document.querySelector('#summary-duration');
  const summaryService = document.querySelector('#summary-service');
  const summaryRenewalRow = document.querySelector('#summary-renewal-row');
  const summaryRenewal = document.querySelector('#summary-renewal');
  const summaryDiscountRow = document.querySelector('#summary-discount-row');
  const summaryDiscount = document.querySelector('#summary-discount');
  const summaryTotal = document.querySelector('#summary-total');
  const summaryDomain = document.querySelector('#summary-domain');
  const summaryDomainCost = document.querySelector('#summary-domain-cost');
  let years = 1;
  let selectedDomain = '';
  let selectedDomainMeta = null;
  let domainState = 'empty';
  let domainResults = [];
  let checkTimer = null;
  let activeController = null;

  const orderedDomainCatalog = () => [...domainCatalog].sort((first, second) => {
    if (first.extension === freeExtension) return -1;
    if (second.extension === freeExtension) return 1;
    return 0;
  });

  const setStatus = (type, message, icon = 'fi-rr-info') => {
    if (!domainStatus || !domainStatusText || !domainStatusIcon) return;
    domainStatus.className = `domain-status${type ? ` is-${type}` : ''}${message ? '' : ' is-empty'}`;
    domainStatusText.textContent = message;
    domainStatusIcon.className = `fi ${icon}`;
  };

  const normalizeDomain = () => {
    const raw = String(domainInput?.value || '').trim().toLowerCase()
      .replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0].replace(/\.$/, '').replace(/\s+/g, '');
    if (domainInput) domainInput.value = raw;
    return raw;
  };

  const domainBase = (value) => {
    const raw = String(value || '').toLowerCase().replace(/\.$/, '');
    const knownSuffix = domainCatalog
      .map((item) => item.extension)
      .sort((first, second) => second.length - first.length)
      .find((suffix) => raw.endsWith(suffix));
    if (knownSuffix) return raw.slice(0, -knownSuffix.length).replace(/\.$/, '');
    if (raw.includes('.')) return raw.split('.').slice(0, -1).join('.');
    return raw;
  };

  const isValidDomainBase = (value) => /^(?=.{1,63}$)(?!.*\.\.)([a-z0-9](?:[a-z0-9.-]*[a-z0-9])?)$/i.test(value);
  const domainCandidates = (base) => orderedDomainCatalog().map((item) => ({ ...item, domain: `${base}${item.extension}`, isFree: item.extension === freeExtension }));
  const domainCost = (domain = selectedDomainMeta) => {
    return !domain || domain.isFree ? 0 : domain.price * years;
  };

  const domainDisplayPrice = (domain = selectedDomainMeta) => {
    if (!domain) return 'Belum dipilih';
    if (domain.isFree) return 'Free';
    return `${formatRupiah(domainCost(domain))}/${years === 1 ? 'tahun' : `${years} tahun`}`;
  };

  const calculateOrder = () => {
    const renewalSubtotal = renewalPrice * (years - 1);
    const serviceSubtotal = basePrice + renewalSubtotal;
    const discountRate = years === 2 ? 0.05 : years === 3 ? 0.1 : 0;
    const discount = Math.round(serviceSubtotal * discountRate);
    const domainFee = domainCost();
    return { renewalSubtotal, serviceSubtotal, discount, domainFee, total: serviceSubtotal - discount + domainFee };
  };

  const renderDomainOptions = (results) => {
    if (!domainOptions) return;
    domainOptions.innerHTML = results.map((result) => {
      const statusClass = result.status === 'available' ? 'is-available' : result.status === 'used' ? 'is-used' : 'is-unknown';
      const selectedClass = result.domain === selectedDomain ? ' is-selected' : '';
      const disabled = result.status !== 'available' ? ' disabled' : '';
      const icon = result.status === 'available' ? 'fi-rr-check-circle' : result.status === 'used' ? 'fi-rr-cross-circle' : 'fi-rr-exclamation';
      const statusText = result.status === 'available' ? 'Tersedia, klik untuk memilih' : result.status === 'used' ? 'Domain sudah digunakan, pilih domain lain' : 'Status belum dapat dipastikan';
      const priceText = domainDisplayPrice(result);
      return `<button class="domain-option ${statusClass}${selectedClass}" type="button" data-domain="${escapeHtml(result.domain)}"${disabled}><span class="domain-option-icon"><i class="fi ${icon}" aria-hidden="true"></i></span><span class="domain-option-copy"><strong class="domain-option-name">${escapeHtml(result.domain)}</strong><small>${statusText}</small></span><span class="domain-option-price"><strong>${priceText}</strong></span></button>`;
    }).join('');
  };

  const updateOrderLink = ({ renewalSubtotal, serviceSubtotal, discount, domainFee, total }) => {
    const domain = selectedDomain || 'belum dipilih';
    const message = [
      'Halo Webkubator, saya ingin memesan paket website.',
      `Jenis website: ${category}`,
      `Paket: ${plan}`,
      `Durasi: ${years} tahun`,
      `Domain: ${domain}`,
      `Biaya domain: ${domainDisplayPrice()}`,
      `Total biaya domain: ${formatRupiah(domainFee)}`,
      'Free hosting: termasuk sesuai paket',
      `Paket ${plan} (tahun pertama): ${formatRupiah(basePrice)}`,
      ...(years > 1 ? [`Perpanjangan (${years - 1} tahun): ${formatRupiah(renewalSubtotal)}`] : []),
      `Harga jasa: ${formatRupiah(serviceSubtotal)}`,
      `Diskon durasi: ${formatRupiah(discount)}`,
      `Total: ${formatRupiah(total)}`,
      'Mohon bantu proses dan konfirmasi detailnya.'
    ].join('\n');
    if (orderLink) {
      orderLink.href = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;
      const enabled = domainState === 'available' && Boolean(selectedDomain);
      orderLink.classList.toggle('is-disabled', !enabled);
      orderLink.setAttribute('aria-disabled', String(!enabled));
    }
  };

  const updateSummary = () => {
    const totals = calculateOrder();
    const { renewalSubtotal, discount, total } = totals;
    if (summaryDuration) summaryDuration.textContent = `${years} tahun`;
    if (summaryService) summaryService.textContent = formatRupiah(basePrice);
    if (summaryRenewalRow) summaryRenewalRow.hidden = years === 1;
    if (summaryRenewal) summaryRenewal.textContent = formatRupiah(renewalSubtotal);
    if (summaryDiscountRow) summaryDiscountRow.hidden = false;
    if (summaryDiscount) summaryDiscount.textContent = discount ? `-${formatRupiah(discount)}` : 'Rp0';
    if (summaryTotal) summaryTotal.textContent = formatRupiah(total);
    if (summaryDomain) summaryDomain.textContent = selectedDomain || 'Belum dipilih';
    if (summaryDomainCost) summaryDomainCost.textContent = domainDisplayPrice();
    updateOrderLink(totals);
  };

  const fetchDomainStatus = async (candidate, signal) => {
    try {
      const response = await fetch(`https://rdap.org/domain/${encodeURIComponent(candidate.domain)}`, {
        headers: { Accept: 'application/rdap+json' },
        cache: 'no-store',
        signal
      });
      return { ...candidate, status: response.status === 404 ? 'available' : response.ok ? 'used' : 'unknown' };
    } catch (error) {
      if (error.name === 'AbortError') throw error;
      return { ...candidate, status: 'unknown' };
    }
  };

  const checkDomain = async () => {
    const raw = normalizeDomain();
    const base = domainBase(raw);
    selectedDomain = '';
    selectedDomainMeta = null;
    domainResults = [];
    domainState = 'empty';
    renderDomainOptions([]);
    updateSummary();
    if (!base) {
      setStatus('', '');
      return;
    }
    if (!isValidDomainBase(base)) {
      domainState = 'invalid';
      setStatus('error', 'Format domain belum benar. Gunakan huruf, angka, tanda hubung, atau titik.', 'fi-rr-cross-circle');
      return;
    }
    activeController?.abort();
    activeController = new AbortController();
    if (domainCheckButton) domainCheckButton.disabled = true;
    setStatus('', 'Sedang memeriksa ketersediaan domain...', 'fi-rr-refresh');
    try {
      domainResults = await Promise.all(domainCandidates(base).map((candidate) => fetchDomainStatus(candidate, activeController.signal)));
      renderDomainOptions(domainResults);
      const available = domainResults.filter((result) => result.status === 'available');
      const unknown = domainResults.some((result) => result.status === 'unknown');
      if (available.length) {
        const preferred = available.find((result) => result.isFree) || available[0];
        selectedDomain = preferred.domain;
        selectedDomainMeta = preferred;
        domainState = 'available';
        renderDomainOptions(domainResults);
        setStatus('success', `${available.length} pilihan domain tersedia. Domain gratis dipilih lebih dulu jika tersedia.`, 'fi-rr-check-circle');
      } else if (unknown) {
        domainState = 'unknown';
        setStatus('warning', 'Sebagian status domain belum dapat dipastikan. Coba periksa lagi.', 'fi-rr-exclamation');
      } else {
        domainState = 'used';
        setStatus('error', 'Semua pilihan domain sudah digunakan. Pilih nama domain lain.', 'fi-rr-cross-circle');
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
      renderDomainOptions(domainResults);
      updateSummary();
    });
  });

  domainOptions?.addEventListener('click', (event) => {
    const option = event.target.closest('button[data-domain]');
    if (!option || option.disabled) return;
    const result = domainResults.find((item) => item.domain === option.dataset.domain);
    if (!result || result.status !== 'available') return;
    selectedDomain = result.domain;
    selectedDomainMeta = result;
    domainState = 'available';
    renderDomainOptions(domainResults);
    setStatus('success', `${selectedDomain} dipilih untuk pesanan.`, 'fi-rr-check-circle');
    updateSummary();
  });

  domainInput?.addEventListener('input', () => {
    selectedDomain = '';
    selectedDomainMeta = null;
    domainResults = [];
    domainState = 'empty';
    clearTimeout(checkTimer);
    renderDomainOptions([]);
    setStatus('', '');
    updateSummary();
    const base = domainBase(normalizeDomain());
    if (base.length >= 3 && isValidDomainBase(base)) checkTimer = window.setTimeout(checkDomain, 700);
  });
  domainInput?.addEventListener('blur', checkDomain);
  domainCheckButton?.addEventListener('click', checkDomain);
  orderLink?.addEventListener('click', (event) => {
    if (orderLink.getAttribute('aria-disabled') === 'true') {
      event.preventDefault();
      domainInput?.focus();
      setStatus('warning', 'Pilih domain yang tersedia terlebih dahulu.', 'fi-rr-exclamation');
    }
  });

  updateSummary();
})();
