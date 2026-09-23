(() => {
  'use strict';
  const money = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0});
  const cartKey = 'webkubator.store.cart.v1';
  const categories = {random:'Random Web',birthday:'Birthday Web',app:'App Web',productivity:'ProductivityWeb','link-bio':'Link Bio','company-profile':'Company Profile','landing-page':'Landing Page','e-commerce':'E-commerce',instansi:'Website Instansi'};
  const $ = (id) => document.getElementById(id);
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const formatPrice = (value) => value === 0 ? 'Gratis' : money.format(value);
  let products = [];
  let cart = [];
  function readCart() {
    try {
      const saved = JSON.parse(localStorage.getItem(cartKey) || '[]'); if (!Array.isArray(saved)) return [];
      return saved.flatMap((item) => {
        if (typeof item === 'string') return [{id:item,variant:'startup',qty:1}];
        if (!item || typeof item.id !== 'string') return [];
        return [{id:item.id,variant:typeof item.variant === 'string' ? item.variant : 'startup',qty:Math.min(20,Math.max(1,Math.round(Number(item.qty) || 1)))}];
      });
    } catch { return []; }
  }
  function writeCart() { localStorage.setItem(cartKey, JSON.stringify(cart)); }
  function updateHeaderCount() {
    const count = cart.reduce((total,item) => total + item.qty, 0); $('cart-count').textContent = String(count); $('cart-link').setAttribute('aria-label', `Keranjang belanja, ${count} produk`);
  }
  function roundMoney(value) { return Math.max(0, Math.round(value / 1000) * 1000); }
  function buildVariants(item) {
    const supplied = Array.isArray(item.variants) ? item.variants.filter((variant) => variant && typeof variant.id === 'string' && Number.isFinite(Number(variant.price))) : [];
    const byId = new Map(supplied.map((variant) => [variant.id,variant]));
    if (['startup','bisnis','premium'].every((id) => byId.has(id))) return ['startup','bisnis','premium'].map((id) => { const variant=byId.get(id); return {id,name:variant.name || id[0].toUpperCase()+id.slice(1),price:Math.max(0,Number(variant.price)),originalPrice:Math.max(0,Number(variant.originalPrice)||0)}; });
    const base = Math.max(0,Number(item.price)||0); const original = Math.max(base,Number(item.originalPrice)||0);
    if (base === 0) return [{id:'startup',name:'Startup',price:0,originalPrice:0},{id:'bisnis',name:'Bisnis',price:69000,originalPrice:99000},{id:'premium',name:'Premium',price:129000,originalPrice:179000}];
    const startupOriginal = original > base ? original : roundMoney(base * 1.2);
    return [{id:'startup',name:'Startup',price:base,originalPrice:startupOriginal},{id:'bisnis',name:'Bisnis',price:roundMoney(base*1.5),originalPrice:roundMoney(startupOriginal*1.5)},{id:'premium',name:'Premium',price:roundMoney(base*2),originalPrice:roundMoney(startupOriginal*2)}];
  }
  function entryData(entry) {
    const product = products.find((item) => item.id === entry.id); if (!product) return null;
    const variants = buildVariants(product); const variant = variants.find((item) => item.id === entry.variant) || variants[0];
    return {entry,product,variant};
  }
  function buildCheckoutUrl(items) {
    const lines = items.map(({entry,product,variant}) => `- ${product.name} | Paket ${variant.name} | ${entry.qty}x | ${formatPrice(variant.price * entry.qty)}`);
    const total = items.reduce((sum,{entry,variant}) => sum + variant.price * entry.qty, 0);
    const message = `Halo Webkubator, saya ingin memesan:\n${lines.join('\n')}\n\nTotal: ${formatPrice(total)}`;
    return `https://wa.me/6287753719307?text=${encodeURIComponent(message)}`;
  }
  function render() {
    const items = cart.map(entryData).filter(Boolean); const totalQuantity = items.reduce((sum,{entry}) => sum + entry.qty,0); const total = items.reduce((sum,{entry,variant}) => sum + variant.price * entry.qty,0);
    if (items.length !== cart.length) { cart = items.map(({entry}) => entry); writeCart(); }
    $('cart-items').replaceChildren(...items.map(({entry,product,variant}) => {
      const article = document.createElement('article'); article.className='cart-item'; article.dataset.cartId=product.id; article.dataset.cartVariant=variant.id;
      const oldPrice = variant.originalPrice > variant.price ? `<del>${escapeHtml(money.format(variant.originalPrice))}</del>` : '';
      article.innerHTML = `<a class="cart-item-media" href="product.html?id=${encodeURIComponent(product.id)}"><img src="${escapeHtml(product.image)}" width="800" height="500" loading="lazy" decoding="async" alt="Preview ${escapeHtml(product.name)}"></a><div class="cart-item-main"><a class="cart-item-title" href="product.html?id=${encodeURIComponent(product.id)}">${escapeHtml(product.name)}</a><span class="cart-item-category">${escapeHtml(categories[product.category] || product.category)} • Paket ${escapeHtml(variant.name)}</span><div class="cart-item-price"><strong>${escapeHtml(formatPrice(variant.price))}</strong>${oldPrice}</div></div><div class="cart-item-side"><strong class="cart-item-total">${escapeHtml(formatPrice(variant.price * entry.qty))}</strong><div class="cart-quantity" aria-label="Jumlah ${escapeHtml(product.name)}"><button type="button" data-cart-action="decrease" aria-label="Kurangi jumlah">−</button><span>${entry.qty}</span><button type="button" data-cart-action="increase" aria-label="Tambah jumlah">+</button></div><button class="cart-remove" type="button" data-cart-action="remove"><i class="fi fi-rr-trash" aria-hidden="true"></i><span>Hapus</span></button></div>`;
      return article;
    }));
    const empty = items.length === 0; $('cart-empty').hidden = !empty; $('cart-clear').hidden = empty; $('cart-list-title').textContent = empty ? 'Produk pilihan' : `Produk pilihan (${items.length})`; $('cart-item-count').textContent = `${totalQuantity} item${totalQuantity === 1 ? '' : ''}`;
    $('summary-quantity').textContent = String(totalQuantity); $('summary-subtotal').textContent = formatPrice(total); $('summary-total').textContent = formatPrice(total); $('cart-checkout').href = empty ? '#' : buildCheckoutUrl(items); $('cart-checkout').setAttribute('aria-disabled', String(empty)); updateHeaderCount();
  }
  function changeQuantity(id,variantId,delta) { const entry=cart.find((item) => item.id===id && item.variant===variantId); if (!entry) return; entry.qty=Math.min(20,Math.max(1,entry.qty+delta)); writeCart(); render(); }
  $('cart-items').addEventListener('click',(event) => { const row=event.target.closest('[data-cart-id]'); const action=event.target.closest('[data-cart-action]')?.dataset.cartAction; if (!row || !action) return; const id=row.dataset.cartId; const variant=row.dataset.cartVariant; if(action==='increase') changeQuantity(id,variant,1); if(action==='decrease') changeQuantity(id,variant,-1); if(action==='remove'){cart=cart.filter((item) => !(item.id===id && item.variant===variant));writeCart();render();} });
  $('cart-clear').addEventListener('click',() => { cart=[]; writeCart(); render(); });
  window.addEventListener('storage',(event) => { if(event.key===cartKey || event.key===null){cart=readCart();render();} });
  async function load() { try { const response=await fetch('catalog.json',{cache:'no-cache'}); if(!response.ok) throw new Error('Catalog unavailable'); const data=await response.json(); products=Array.isArray(data.products) ? data.products.filter((item) => item && typeof item.id==='string') : []; cart=readCart(); render(); } catch { $('cart-empty').hidden=false; $('cart-items').replaceChildren(); $('cart-item-count').textContent='Katalog belum termuat'; } }
  load();
})();

