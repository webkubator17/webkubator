(() => {
  'use strict';
  const money = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0});
  const cartKey = 'webkubator.store.cart.v1';
  const categories = {
    random:'Random Web', birthday:'Birthday Web', app:'App Web', productivity:'ProductivityWeb',
    'link-bio':'Link Bio', 'company-profile':'Company Profile', 'landing-page':'Landing Page',
    'e-commerce':'E-commerce', instansi:'Website Instansi'
  };
  const $ = (id) => document.getElementById(id);
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const icon = (name) => `<i class="fi ${name}" aria-hidden="true"></i>`;
  let product;
  let media = [];
  let activeMedia = 0;
  let feedbackTimer;
  const notify = (message) => {
    let toast = $('product-feedback');
    if (!toast) { toast = document.createElement('p'); toast.id = 'product-feedback'; toast.className = 'toast'; toast.setAttribute('role','status'); toast.setAttribute('aria-live','polite'); document.body.append(toast); }
    clearTimeout(feedbackTimer); toast.textContent = message; toast.classList.add('is-visible');
    feedbackTimer = setTimeout(() => toast.classList.remove('is-visible'), 3200);
  };
  function readCart() {
    try { const saved = JSON.parse(localStorage.getItem(cartKey) || '[]'); return Array.isArray(saved) ? saved.filter((id) => typeof id === 'string') : []; }
    catch { return []; }
  }
  function updateCartCount() {
    const count = readCart().length;
    const counter = $('cart-count');
    const link = $('cart-link');
    counter.textContent = String(count);
    link.setAttribute('aria-label', `Keranjang belanja, ${count} produk`);
  }
  function addToCart() {
    if (!product) return;
    const cart = readCart();
    const alreadyInCart = cart.includes(product.id);
    if (!alreadyInCart) cart.push(product.id);
    localStorage.setItem(cartKey, JSON.stringify(cart));
    updateCartCount();
    notify(alreadyInCart ? 'Produk sudah ada di keranjang.' : `${product.name} ditambahkan ke keranjang.`);
  }
  function productDetails(item) {
    const type = item.type || 'Website siap pakai';
    const packageName = item.package || categories[item.category] || 'Paket website';
    return {
      type, packageName, format:item.format || 'Template website',
      description:item.description || `Website ${String(item.name).toLowerCase()} dengan tampilan modern, responsif, dan siap dikembangkan sesuai kebutuhan bisnis Anda.`,
      features:Array.isArray(item.features) && item.features.length ? item.features : ['Desain responsif untuk semua device','Struktur halaman siap dikustomisasi','Komponen antarmuka modern','Optimasi tampilan dan performa','Dokumentasi penggunaan dasar'],
      benefits:Array.isArray(item.benefits) && item.benefits.length ? item.benefits : ['Mempercepat proses website Anda online','Tampilan bisnis lebih profesional','Mudah disesuaikan dengan identitas brand','Nyaman digunakan oleh pengunjung','Cocok untuk kebutuhan bisnis modern'],
      technologies:Array.isArray(item.technologies) && item.technologies.length ? item.technologies : ['HTML5','CSS3','JavaScript']
    };
  }
  function setList(element, values) { element.replaceChildren(...values.map((value) => { const li = document.createElement('li'); li.textContent = value; return li; })); }
  function setTechnologies(values) { $('detail-technologies').replaceChildren(...values.map((value) => { const span = document.createElement('span'); span.textContent = value; return span; })); }
  function buildWhatsApp(label) {
    const price = product.price === 0 ? 'Gratis' : money.format(product.price);
    const message = `Halo Webkubator, saya ingin ${label} produk "${product.name}". Harga: ${price}.`;
    return `https://wa.me/6287753719307?text=${encodeURIComponent(message)}`;
  }
  function renderProduct() {
    const details = productDetails(product);
    const price = product.price === 0 ? 'Gratis' : money.format(product.price);
    $('detail-price').textContent = price;
    $('detail-price').classList.toggle('is-free', product.price === 0);
    $('detail-title').textContent = product.name;
    $('product-meta').replaceChildren(...[['Jenis',details.type],['Paket',details.packageName],['Tipe',details.format]].map(([label,value]) => {
      const item = document.createElement('div'); item.className = 'product-meta-item';
      item.innerHTML = `<span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong>`; return item;
    }));
    $('detail-description').textContent = details.description;
    setList($('detail-features'), details.features); setList($('detail-benefits'), details.benefits); setTechnologies(details.technologies);
    const gallery = Array.isArray(product.gallery) ? product.gallery.filter((src) => typeof src === 'string' && src) : [];
    const imageSources = gallery.length ? gallery : [product.image];
    media = imageSources.map((src) => ({type:'image',src}));
    if (typeof product.video === 'string' && product.video) media.push({type:'video',src:product.video});
    renderMedia();
    const chatUrl = buildWhatsApp('konsultasi tentang');
    const buyUrl = buildWhatsApp('membeli');
    for (const link of [$('desktop-chat'),$('mobile-chat')]) link.href = chatUrl;
    for (const link of [$('desktop-buy'),$('mobile-buy')]) link.href = buyUrl;
    for (const priceTarget of [$('desktop-buy-price'),$('mobile-buy-price')]) priceTarget.textContent = price;
    for (const button of [$('desktop-cart'),$('mobile-cart')]) button.addEventListener('click', addToCart);
    $('product-layout').hidden = false; $('mobile-action-bar').hidden = false;
    document.title = `${product.name} — Webkubator Store`;
  }
  function renderMedia() {
    const item = media[activeMedia] || media[0];
    const image = $('product-image'); const video = $('product-video');
    image.hidden = item.type !== 'image'; video.hidden = item.type !== 'video';
    if (item.type === 'image') { image.src = item.src; image.alt = `Preview ${product.name}, gambar ${activeMedia + 1}`; video.pause(); video.removeAttribute('src'); }
    else { video.src = item.src; video.querySelector('source').src = item.src; video.load(); image.removeAttribute('src'); }
    const multiple = media.length > 1;
    $('media-previous').hidden = !multiple; $('media-next').hidden = !multiple;
    $('media-counter').hidden = !multiple; $('media-counter').textContent = `${activeMedia + 1} / ${media.length}`;
    const thumbs = $('media-thumbs'); thumbs.hidden = !multiple; thumbs.replaceChildren(...media.map((entry,index) => {
      const button = document.createElement('button'); button.type = 'button'; button.className = 'media-thumb'; button.dataset.mediaIndex = String(index); button.setAttribute('aria-current', String(index === activeMedia)); button.setAttribute('aria-label', `${entry.type === 'video' ? 'Video' : 'Gambar'} ${index + 1}`);
      if (entry.type === 'image') { const img = document.createElement('img'); img.src = entry.src; img.alt = ''; img.loading = 'lazy'; button.append(img); }
      else { button.classList.add('media-thumb-video'); button.innerHTML = `${icon('fi-rr-play-alt')}<span class="sr-only">Video</span>`; }
      return button;
    }));
  }
  function showMedia(index) { activeMedia = (index + media.length) % media.length; renderMedia(); }
  function showError() { $('product-error').hidden = false; $('product-layout').hidden = true; $('mobile-action-bar').hidden = true; }
  $('media-previous').addEventListener('click', () => showMedia(activeMedia - 1));
  $('media-next').addEventListener('click', () => showMedia(activeMedia + 1));
  $('media-thumbs').addEventListener('click', (event) => { const button = event.target.closest('[data-media-index]'); if (button) showMedia(Number(button.dataset.mediaIndex)); });
  window.addEventListener('keydown', (event) => { if (media.length < 2 || event.target.matches('input,textarea,select,button,a')) return; if (event.key === 'ArrowLeft') showMedia(activeMedia - 1); if (event.key === 'ArrowRight') showMedia(activeMedia + 1); });
  window.addEventListener('storage', (event) => { if (event.key === cartKey || event.key === null) updateCartCount(); });
  async function load() {
    updateCartCount();
    const id = new URLSearchParams(location.search).get('id');
    try {
      const response = await fetch('catalog.json',{cache:'no-cache'}); if (!response.ok) throw new Error('Catalog unavailable');
      const data = await response.json(); product = Array.isArray(data.products) ? data.products.find((item) => item && item.id === id) : null;
      if (!product || typeof product.image !== 'string') throw new Error('Product not found');
      renderProduct();
    } catch { showError(); }
  }
  load();
})();

