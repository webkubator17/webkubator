(() => {
  'use strict';
  const categories = [
    ['all', 'Semua Produk', 'fi-rr-apps'], ['random', 'Random Web', 'fi-rr-shuffle'],
    ['birthday', 'Birthday Web', 'fi-rr-cake-birthday'], ['app', 'App Web', 'fi-rr-mobile'],
    ['productivity', 'ProductivityWeb', 'fi-rr-list-check'], ['link-bio', 'Link Bio', 'fi-rr-link'],
    ['company-profile', 'Company Profile', 'fi-rr-building'], ['landing-page', 'Landing Page', 'fi-rr-browser'],
    ['e-commerce', 'E-commerce', 'fi-rr-shopping-cart'], ['instansi', 'Website Instansi', 'fi-rr-bank']
  ];
  const $ = (id) => document.getElementById(id);
  const grid = $('product-grid');
  const track = $('categories');
  const dialog = $('filter-dialog');
  const form = $('filter-form');
  const storageKey = 'webkubator.store.favorites.v1';
  const state = {category:'all', query:'', price:'all', discount:false, sort:'default', favoritesOnly:false};
  const money = new Intl.NumberFormat('id-ID', {style:'currency',currency:'IDR',maximumFractionDigits:0});
  let products = [];
  let favorites = new Set();
  let catalogLoaded = false;
  let feedbackTimer;
  const icon = (name) => `<i class="fi ${name}" aria-hidden="true"></i>`;
  const categoryName = (id) => categories.find((item) => item[0] === id)?.[1] || id;
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const notify = (message) => {
    clearTimeout(feedbackTimer);
    $('feedback').textContent = message;
    $('feedback').classList.add('is-visible');
    feedbackTimer = setTimeout(() => $('feedback').classList.remove('is-visible'), 3500);
  };
  function readFavorites() {
    try {
      const saved = JSON.parse(localStorage.getItem(storageKey) || '[]');
      return new Set(Array.isArray(saved) ? saved.filter((id) => typeof id === 'string') : []);
    } catch { return new Set(); }
  }
  function updateFavoriteCount() {
    const count = products.filter((product) => favorites.has(product.id)).length;
    $('favorites-count').textContent = count;
    $('favorites-toggle').setAttribute('aria-pressed', String(state.favoritesOnly));
    $('favorites-toggle').setAttribute('aria-label', `${state.favoritesOnly ? 'Tutup' : 'Lihat'} favorit, ${count} produk`);
  }
  function syncUrl() {
    const url = new URL(location.href);
    [['category',state.category === 'all' ? '' : state.category], ['q',state.query],
      ['price',state.price === 'all' ? '' : state.price], ['sort',state.sort === 'default' ? '' : state.sort],
      ['discount',state.discount ? '1' : ''], ['favorites',state.favoritesOnly ? '1' : '']
    ].forEach(([key,value]) => value ? url.searchParams.set(key,value) : url.searchParams.delete(key));
    history.replaceState(null,'',url);
  }
  function readUrl() {
    const query = new URLSearchParams(location.search);
    state.category = categories.some(([id]) => id === query.get('category')) ? query.get('category') : 'all';
    state.query = (query.get('q') || '').slice(0,100);
    state.price = ['free','paid'].includes(query.get('price')) ? query.get('price') : 'all';
    state.sort = ['price-asc','price-desc','name'].includes(query.get('sort')) ? query.get('sort') : 'default';
    state.discount = query.get('discount') === '1';
    state.favoritesOnly = query.get('favorites') === '1';
    $('product-search').value = state.query;
  }
  function visibleProducts() {
    const query = state.query.trim().toLocaleLowerCase('id');
    const result = products.filter((product) =>
      (state.category === 'all' || product.category === state.category) &&
      (!query || `${product.name} ${categoryName(product.category)}`.toLocaleLowerCase('id').includes(query)) &&
      (state.price === 'all' || (state.price === 'free' ? product.price === 0 : product.price > 0)) &&
      (!state.discount || product.originalPrice > product.price) &&
      (!state.favoritesOnly || favorites.has(product.id))
    );
    if (state.sort === 'price-asc') result.sort((a,b) => a.price - b.price);
    if (state.sort === 'price-desc') result.sort((a,b) => b.price - a.price);
    if (state.sort === 'name') result.sort((a,b) => a.name.localeCompare(b.name,'id'));
    return result;
  }
  function render() {
    const visible = visibleProducts();
    grid.replaceChildren();
    visible.forEach((product,index) => {
      const card = document.createElement('article');
      const liked = favorites.has(product.id);
      card.className = 'product-card';
      card.dataset.productId = product.id;
      // Text is escaped; images are restricted to store previews and portfolio assets.
      card.innerHTML = `<div class="product-visual">
        <img src="${escapeHtml(product.image)}" width="800" height="450" loading="${index < 2 ? 'eager' : 'lazy'}" decoding="async" alt="Contoh preview ${escapeHtml(product.name)}">
        <button class="product-favorite" type="button" data-favorite="${escapeHtml(product.id)}" aria-pressed="${liked}" aria-label="${liked ? 'Hapus' : 'Simpan'} ${escapeHtml(product.name)} ${liked ? 'dari' : 'ke'} favorit">${icon('fi-rr-heart')}</button>
      </div><div class="product-info"><span class="product-category">${escapeHtml(categoryName(product.category))}</span>
        <h2>${escapeHtml(product.name)}</h2><p class="price-line">
        <strong class="current-price${product.price === 0 ? ' price-free' : ''}">${product.price === 0 ? 'Gratis' : money.format(product.price)}</strong>
        ${product.originalPrice > product.price ? `<del class="old-price" aria-label="Harga sebelumnya ${money.format(product.originalPrice)}">${money.format(product.originalPrice)}</del>` : ''}
      </p></div>`;
      grid.append(card);
    });
    $('result-count').textContent = `${visible.length} produk${state.favoritesOnly ? ' favorit' : ''}`;
    $('empty-state').hidden = visible.length !== 0;
    $('empty-title').textContent = state.favoritesOnly && !products.some((p) => favorites.has(p.id)) ? 'Belum ada favorit' : 'Produk tidak ditemukan';
    $('empty-copy').textContent = state.favoritesOnly && !products.some((p) => favorites.has(p.id)) ? 'Ketuk ikon hati pada produk untuk menyimpannya di sini.' : 'Coba kata kunci, kategori, atau filter lainnya.';
    track.querySelectorAll('button').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.category === state.category)));
    const filterCount = Number(state.price !== 'all') + Number(state.discount) + Number(state.sort !== 'default');
    $('filter-count').hidden = !filterCount;
    $('filter-count').textContent = filterCount;
    updateFavoriteCount();
    syncUrl();
  }
  function resetFilters(includeFavorites = false) {
    Object.assign(state,{category:'all',query:'',price:'all',discount:false,sort:'default'});
    if (includeFavorites) state.favoritesOnly = false;
    $('product-search').value = '';
    render();
    track.scrollTo({left:0,behavior:'auto'});
  }
  function updateSlider() {
    document.querySelector('.previous').disabled = track.scrollLeft <= 2;
    document.querySelector('.next').disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
  }
  categories.forEach(([id,label,name]) => {
    const button = document.createElement('button');
    button.type = 'button';button.className = 'category';button.dataset.category = id;
    button.innerHTML = `${icon(name)}<span>${label}</span>`;
    button.setAttribute('aria-pressed', String(id === 'all'));button.disabled = true;
    track.append(button);
  });
  track.addEventListener('click',(event) => {
    const button = event.target.closest('[data-category]');
    if (!button) return;
    state.category = button.dataset.category;render();
    button.scrollIntoView({block:'nearest',inline:'nearest',behavior:matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'});
  });
  for (const [selector,direction] of [['.previous',-1],['.next',1]]) {
    document.querySelector(selector).addEventListener('click',() => track.scrollBy({left:direction * track.clientWidth * .65,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'}));
  }
  track.addEventListener('scroll', updateSlider,{passive:true});
  window.addEventListener('resize',updateSlider,{passive:true});
  $('search-form').addEventListener('submit',(event) => event.preventDefault());
  $('product-search').addEventListener('input',(event) => {state.query = event.target.value;render();});
  $('favorites-toggle').addEventListener('click',() => {state.favoritesOnly = !state.favoritesOnly;render();});
  grid.addEventListener('click',(event) => {
    const button = event.target.closest('[data-favorite]');
    if (!button) return;
    const id = button.dataset.favorite;
    const product = products.find((entry) => entry.id === id);
    if (!product) return;
    favorites.has(id) ? favorites.delete(id) : favorites.add(id);
    const liked = favorites.has(id);
    let stored = true;
    try {localStorage.setItem(storageKey,JSON.stringify([...favorites]));} catch {stored = false;}
    if (state.favoritesOnly) {
      const oldIndex = [...grid.querySelectorAll('[data-favorite]')].indexOf(button);
      render();
      const remaining = [...grid.querySelectorAll('[data-favorite]')];
      (remaining[Math.min(oldIndex,remaining.length-1)] || $('empty-reset')).focus();
    } else {
      button.setAttribute('aria-pressed',String(liked));
      button.setAttribute('aria-label',`${liked ? 'Hapus' : 'Simpan'} ${product.name} ${liked ? 'dari' : 'ke'} favorit`);
      updateFavoriteCount();
    }
    notify(stored ? `${product.name} ${liked ? 'disimpan ke' : 'dihapus dari'} favorit.` : 'Favorit berubah untuk sesi ini. Penyimpanan browser tidak tersedia.');
  });
  $('filter-toggle').addEventListener('click',() => {
    form.elements.price.value = state.price;form.elements.discount.checked = state.discount;form.elements.sort.value = state.sort;
    dialog.showModal();document.body.style.overflow = 'hidden';
  });
  $('filter-close').addEventListener('click',() => dialog.close());
  dialog.addEventListener('close',() => {document.body.style.overflow = ''; $('filter-toggle').focus();});
  dialog.addEventListener('click',(event) => {if (event.target === dialog) {const rect = dialog.getBoundingClientRect();if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();}});
  $('filter-reset').addEventListener('click',() => form.reset());
  form.addEventListener('submit',(event) => {event.preventDefault();state.price = form.elements.price.value;state.discount = form.elements.discount.checked;state.sort = form.elements.sort.value;render();dialog.close();});
  $('reset-filters').addEventListener('click',() => resetFilters(true));
  $('empty-reset').addEventListener('click',() => {
    if (!catalogLoaded) {loadCatalog();return;}
    resetFilters(true);$('product-search').focus();
  });
  window.addEventListener('storage',(event) => {if ((event.key === storageKey || event.key === null) && catalogLoaded) {favorites = readFavorites();render();}});
  window.addEventListener('popstate',() => {if (catalogLoaded) {readUrl();render();}});
  async function loadCatalog() {
    $('empty-state').hidden = true;
    grid.setAttribute('aria-busy','true');
    $('result-count').textContent = 'Memuat produk…';
    try {
      const response = await fetch('catalog.json',{cache:'no-cache'});
      if (!response.ok) throw new Error('Catalog unavailable');
      const data = await response.json();
      if (!Array.isArray(data.products)) throw new Error('Invalid catalog');
      const ids = new Set();
      products = data.products.filter((product) => {
        const valid = product && typeof product.id === 'string' && !ids.has(product.id) && typeof product.name === 'string' && categories.some(([id]) => id !== 'all' && id === product.category) && Number.isFinite(product.price) && product.price >= 0 && Number.isFinite(product.originalPrice) && product.originalPrice >= 0 && typeof product.image === 'string' && /^(?:previews\.svg#[a-z-]+|images\/[a-zA-Z0-9_-]+\.(?:webp|avif|png|jpg|jpeg)|\.\.\/assets\/images\/portfolio-[a-z-]+\.webp)$/.test(product.image);
        if (valid) ids.add(product.id);
        return valid;
      });
      favorites = readFavorites();catalogLoaded = true;readUrl();
      $('empty-reset').textContent = 'Lihat semua produk';
      for (const control of [$('favorites-toggle'),$('product-search'),$('filter-toggle'),...track.querySelectorAll('button')]) control.disabled = false;
      render();updateSlider();
      if (document.fonts) document.fonts.ready.then(updateSlider);
    } catch {
      $('result-count').textContent = 'Katalog belum termuat';
      $('empty-title').textContent = 'Katalog belum bisa dibuka';
      $('empty-copy').textContent = 'Periksa koneksi internet Anda, lalu coba lagi.';
      $('empty-reset').textContent = 'Coba lagi';$('empty-state').hidden = false;
    } finally {grid.setAttribute('aria-busy','false');}
  }
  loadCatalog();
})();
