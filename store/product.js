(() => {
  'use strict';
  const money = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0});
  const cartKey = 'webkubator.store.cart.v1';
  const $ = (id) => document.getElementById(id);
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const icon = (name) => `<i class="fi ${name}" aria-hidden="true"></i>`;
  let product;
  let variants = [];
  let selectedVariantId = 'startup';
  let quantity = 1;
  let media = [];
  let activeMedia = 0;
  let feedbackTimer;
  let zoomTrigger = null;

  const notify = (message) => {
    let toast = $('product-feedback');
    if (!toast) { toast = document.createElement('p'); toast.id = 'product-feedback'; toast.className = 'toast'; toast.setAttribute('role','status'); toast.setAttribute('aria-live','polite'); document.body.append(toast); }
    clearTimeout(feedbackTimer); toast.textContent = message; toast.classList.add('is-visible');
    feedbackTimer = setTimeout(() => toast.classList.remove('is-visible'), 3200);
  };
  function readCart() {
    try {
      const saved = JSON.parse(localStorage.getItem(cartKey) || '[]');
      if (!Array.isArray(saved)) return [];
      return saved.flatMap((item) => {
        if (typeof item === 'string') return [{id:item,variant:'startup',qty:1}];
        if (!item || typeof item.id !== 'string') return [];
        return [{id:item.id,variant:typeof item.variant === 'string' ? item.variant : 'startup',qty:Math.min(20,Math.max(1,Math.round(Number(item.qty) || 1)))}];
      });
    } catch { return []; }
  }
  function writeCart(cart) { localStorage.setItem(cartKey, JSON.stringify(cart)); }
  function cartCount() { return readCart().reduce((total,item) => total + item.qty, 0); }
  function updateCartCount() {
    const count = cartCount();
    $('cart-count').textContent = String(count);
    $('cart-link').setAttribute('aria-label', `Keranjang belanja, ${count} produk`);
  }
  function roundMoney(value) { return Math.max(0, Math.round(value / 1000) * 1000); }
  function buildVariants(item) {
    const supplied = Array.isArray(item.variants) ? item.variants.filter((variant) => variant && typeof variant.id === 'string' && Number.isFinite(Number(variant.price))) : [];
    const suppliedById = new Map(supplied.map((variant) => [variant.id, variant]));
    if (['startup','bisnis','premium'].every((id) => suppliedById.has(id))) {
      return ['startup','bisnis','premium'].map((id) => {
        const variant = suppliedById.get(id);
        return {id, name:variant.name || id[0].toUpperCase() + id.slice(1), price:Math.max(0,Number(variant.price)), originalPrice:Math.max(0,Number(variant.originalPrice) || 0)};
      });
    }
    const basePrice = Math.max(0, Number(item.price) || 0);
    const baseOriginal = Math.max(basePrice, Number(item.originalPrice) || 0);
    if (basePrice === 0) {
      return [
        {id:'startup',name:'Startup',price:0,originalPrice:0},
        {id:'bisnis',name:'Bisnis',price:69000,originalPrice:99000},
        {id:'premium',name:'Premium',price:129000,originalPrice:179000}
      ];
    }
    const startupOriginal = baseOriginal > basePrice ? baseOriginal : roundMoney(basePrice * 1.2);
    return [
      {id:'startup',name:'Startup',price:basePrice,originalPrice:startupOriginal},
      {id:'bisnis',name:'Bisnis',price:roundMoney(basePrice * 1.5),originalPrice:roundMoney(startupOriginal * 1.5)},
      {id:'premium',name:'Premium',price:roundMoney(basePrice * 2),originalPrice:roundMoney(startupOriginal * 2)}
    ];
  }
  function activeVariant() { return variants.find((variant) => variant.id === selectedVariantId) || variants[0]; }
  function formatPrice(value) { return value === 0 ? 'Gratis' : money.format(value); }
  function productDetails(item) {
    return {
      description:item.description || `Website ${String(item.name).toLowerCase()} dengan tampilan modern, responsif, dan siap dikembangkan sesuai kebutuhan bisnis Anda.`,
      features:Array.isArray(item.features) && item.features.length ? item.features : ['Desain responsif untuk semua device','Struktur halaman siap dikustomisasi','Komponen antarmuka modern','Optimasi tampilan dan performa','Dokumentasi penggunaan dasar'],
      benefits:Array.isArray(item.benefits) && item.benefits.length ? item.benefits : ['Mempercepat proses website Anda online','Tampilan bisnis lebih profesional','Mudah disesuaikan dengan identitas brand','Nyaman digunakan oleh pengunjung','Cocok untuk kebutuhan bisnis modern'],
      technologies:Array.isArray(item.technologies) && item.technologies.length ? item.technologies : ['HTML5','CSS3','JavaScript']
    };
  }
  function setList(element, values) { element.replaceChildren(...values.map((value) => { const li = document.createElement('li'); li.textContent = value; return li; })); }
  function setTechnologies(values) { $('detail-technologies').replaceChildren(...values.map((value) => { const span = document.createElement('span'); span.textContent = value; return span; })); }
  function buildWhatsApp(label) {
    const variant = activeVariant();
    const total = variant.price * quantity;
    const message = `Halo Webkubator, saya ingin ${label} produk "${product.name}". Paket: ${variant.name}. Jumlah: ${quantity}. Total: ${formatPrice(total)}.`;
    return `https://wa.me/6287753719307?text=${encodeURIComponent(message)}`;
  }
  function updatePurchaseLinks() {
    const chatUrl = buildWhatsApp('konsultasi tentang');
    const buyUrl = buildWhatsApp('membeli');
    for (const link of [$('desktop-chat'),$('mobile-chat')]) link.href = chatUrl;
    for (const link of [$('desktop-buy'),$('mobile-buy')]) link.href = buyUrl;
  }
  function updatePricing() {
    const variant = activeVariant();
    if (!variant) return;
    const total = variant.price * quantity;
    const originalTotal = variant.originalPrice * quantity;
    $('detail-price').textContent = formatPrice(total);
    $('detail-price').classList.toggle('is-free', total === 0);
    const original = $('detail-original-price');
    original.hidden = !(originalTotal > total); original.textContent = originalTotal > total ? money.format(originalTotal) : '';
    const discount = $('detail-discount');
    discount.hidden = !(originalTotal > total); discount.textContent = originalTotal > total ? `Diskon ${Math.round((1 - total / originalTotal) * 100)}%` : '';
    $('desktop-buy-price').textContent = formatPrice(total); $('mobile-buy-price').textContent = formatPrice(total);
    updatePurchaseLinks();
  }
  function renderVariants() {
    const options = $('variant-options');
    options.replaceChildren(...variants.map((variant) => {
      const button = document.createElement('button'); button.type = 'button'; button.className = 'variant-option'; button.dataset.variantId = variant.id; button.setAttribute('aria-pressed', String(variant.id === selectedVariantId));
      const oldPrice = variant.originalPrice > variant.price ? `<del>${escapeHtml(money.format(variant.originalPrice))}</del>` : '';
      button.innerHTML = `<strong>${escapeHtml(variant.name)}</strong><small>${escapeHtml(formatPrice(variant.price))}</small>${oldPrice}`;
      return button;
    }));
  }
  function setQuantity(value) { quantity = Math.min(20, Math.max(1, Math.round(Number(value) || 1))); $('quantity').value = String(quantity); updatePricing(); }
  function addToCart() {
    if (!product || !activeVariant()) return;
    const cart = readCart(); const variant = activeVariant();
    const existing = cart.find((item) => item.id === product.id && item.variant === variant.id);
    if (existing) existing.qty = Math.min(20, existing.qty + quantity); else cart.push({id:product.id,variant:variant.id,qty:quantity});
    writeCart(cart); updateCartCount(); notify(existing ? 'Jumlah produk di keranjang diperbarui.' : `${product.name} ditambahkan ke keranjang.`);
  }
  function renderProduct() {
    const details = productDetails(product);
    $('detail-title').textContent = product.name; $('detail-description').textContent = details.description;
    setList($('detail-features'), details.features); setList($('detail-benefits'), details.benefits); setTechnologies(details.technologies);
    variants = buildVariants(product); selectedVariantId = variants.some((variant) => variant.id === product.defaultVariant) ? product.defaultVariant : 'startup';
    renderVariants(); setQuantity(1);
    const gallery = Array.isArray(product.gallery) ? product.gallery.filter((src) => typeof src === 'string' && src) : [];
    media = (gallery.length ? gallery : [product.image]).map((src) => ({type:'image',src}));
    if (typeof product.video === 'string' && product.video) media.push({type:'video',src:product.video});
    renderMedia();
    for (const button of [$('desktop-cart'),$('mobile-cart')]) button.addEventListener('click', addToCart);
    $('product-layout').hidden = false; $('mobile-action-bar').hidden = false; document.title = `${product.name} — Webkubator Store`;
  }
  function renderMedia() {
    const item = media[activeMedia] || media[0]; const image = $('product-image'); const video = $('product-video'); const zoom = $('media-zoom');
    image.hidden = item.type !== 'image'; video.hidden = item.type !== 'video'; zoom.hidden = item.type !== 'image';
    if (item.type === 'image') {
      image.src = item.src; image.alt = `Preview ${product.name}, gambar ${activeMedia + 1}`; video.pause(); video.removeAttribute('src'); video.querySelector('source').removeAttribute('src'); zoom.setAttribute('aria-label', `Perbesar foto produk ${product.name}`);
    } else { video.removeAttribute('src'); video.querySelector('source').src = item.src; video.load(); image.removeAttribute('src'); }
    const multiple = media.length > 1;
    $('media-previous').hidden = !multiple; $('media-next').hidden = !multiple; $('media-counter').hidden = !multiple; $('media-counter').textContent = `${activeMedia + 1} / ${media.length}`;
    const thumbs = $('media-thumbs'); thumbs.hidden = !multiple;
    thumbs.replaceChildren(...media.map((entry,index) => {
      const button = document.createElement('button'); button.type = 'button'; button.className = 'media-thumb'; button.dataset.mediaIndex = String(index); button.setAttribute('aria-current', String(index === activeMedia)); button.setAttribute('aria-label', `${entry.type === 'video' ? 'Video' : 'Gambar'} ${index + 1}`);
      if (entry.type === 'image') { const img = document.createElement('img'); img.src = entry.src; img.alt = ''; img.loading = 'lazy'; button.append(img); } else { button.classList.add('media-thumb-video'); button.innerHTML = `${icon('fi-rr-play-alt')}<span class="sr-only">Video</span>`; }
      return button;
    }));
  }
  function showMedia(index) { activeMedia = (index + media.length) % media.length; renderMedia(); }
  function showError() { $('product-error').hidden = false; $('product-layout').hidden = true; $('mobile-action-bar').hidden = true; }
  function openZoom() { if (!media[activeMedia] || media[activeMedia].type !== 'image') return; zoomTrigger = $('media-zoom'); $('zoom-image').src = media[activeMedia].src; $('zoom-image').alt = `Preview besar ${product.name}`; $('media-dialog').showModal(); document.body.style.overflow = 'hidden'; }
  $('media-previous').addEventListener('click', () => showMedia(activeMedia - 1)); $('media-next').addEventListener('click', () => showMedia(activeMedia + 1)); $('media-zoom').addEventListener('click', openZoom);
  $('media-thumbs').addEventListener('click', (event) => { const button = event.target.closest('[data-media-index]'); if (button) showMedia(Number(button.dataset.mediaIndex)); });
  $('media-dialog-close').addEventListener('click', () => $('media-dialog').close()); $('media-dialog').addEventListener('close', () => { document.body.style.overflow = ''; $('zoom-image').removeAttribute('src'); if (zoomTrigger) zoomTrigger.focus(); zoomTrigger = null; });
  $('media-dialog').addEventListener('click', (event) => { if (event.target === $('media-dialog')) $('media-dialog').close(); });
  $('variant-options').addEventListener('click', (event) => { const button = event.target.closest('[data-variant-id]'); if (!button) return; selectedVariantId = button.dataset.variantId; renderVariants(); updatePricing(); });
  $('quantity-minus').addEventListener('click', () => setQuantity(quantity - 1)); $('quantity-plus').addEventListener('click', () => setQuantity(quantity + 1)); $('quantity').addEventListener('change', (event) => setQuantity(event.target.value));
  window.addEventListener('keydown', (event) => { if (media.length < 2 || event.target.matches('input,textarea,select,button,a')) return; if (event.key === 'ArrowLeft') showMedia(activeMedia - 1); if (event.key === 'ArrowRight') showMedia(activeMedia + 1); });
  window.addEventListener('storage', (event) => { if (event.key === cartKey || event.key === null) updateCartCount(); });
  async function load() {
    updateCartCount(); const id = new URLSearchParams(location.search).get('id');
    try { const response = await fetch('catalog.json',{cache:'no-cache'}); if (!response.ok) throw new Error('Catalog unavailable'); const data = await response.json(); product = Array.isArray(data.products) ? data.products.find((item) => item && item.id === id) : null; if (!product || typeof product.image !== 'string') throw new Error('Product not found'); renderProduct(); }
    catch { showError(); }
  }
  load();
})();

