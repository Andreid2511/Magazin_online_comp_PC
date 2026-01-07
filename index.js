(function () {
  'use strict';

  const CART_KEY = 'fr_cart_v1';

  // --- 1. HELPER FUNCTIONS ---
  function qs(sel, ctx = document) { return ctx.querySelector(sel); }
  function qsa(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }

  function loadCart() {
    try {
      const raw = localStorage.getItem(CART_KEY);
      return raw ? JSON.parse(raw) : {};
    } catch (e) {
      console.error('Failed to load cart', e);
      return {};
    }
  }

  function saveCart(cart) {
    try { localStorage.setItem(CART_KEY, JSON.stringify(cart)); }
    catch (e) { console.error('Failed to save cart', e); }
  }

  function cartTotalItems(cart) {
    return Object.values(cart).reduce((sum, it) => sum + (it.qty || 0), 0);
  }

  // --- 2. BADGE LOGIC ---
  function ensureCartBadge() {
    const cartLink = qs('a[href$="cosul_meu.php"]') || qs('a[href*="cosul_meu"]');
    if (!cartLink) return null;
    let badge = qs('#cart-count-badge', cartLink);
    if (!badge) {
      badge = document.createElement('span');
      badge.id = 'cart-count-badge';
      badge.style.background = '#0070f3';
      badge.style.color = '#fff';
      badge.style.borderRadius = '999px';
      badge.style.padding = '0 .45rem';
      badge.style.marginLeft = '.4rem';
      badge.style.fontSize = '.9rem';
      badge.style.fontWeight = '700';
      badge.style.display = 'inline-block';
      badge.textContent = '0';
      cartLink.appendChild(badge);
    }
    return badge;
  }

  function updateCartBadge() {
    const cart = loadCart();
    const badge = ensureCartBadge();
    if (!badge) return;
    const total = cartTotalItems(cart);
    badge.textContent = String(total);
    badge.style.display = total > 0 ? 'inline-block' : 'none';
  }

  // --- 3. ADD TO CART LOGIC ---
  function attachAddToCart() {
    const buttons = qsa('button.add-to-cart, .add-to-cart');
    if (!buttons.length) return;

    buttons.forEach(btn => {
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);

      newBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const card = newBtn.closest('.card') || newBtn.closest('.product-card') || newBtn.parentElement;
        if (!card) return;

        const titleEl = qs('[data-product-title]', card) || qs('h3', card);
        const priceEl = qs('[data-product-price]', card) || qs('.price', card);
        const imgEl = qs('[data-product-image]', card) || qs('img', card);

        const title = titleEl ? (titleEl.getAttribute('data-product-title') || titleEl.textContent.trim()) : 'Product';
        const price = priceEl ? (priceEl.getAttribute('data-product-price') || priceEl.textContent.replace(/[^0-9.,-]/g, '').trim()) : '0';
        const image = imgEl ? (imgEl.getAttribute('data-product-image') || imgEl.getAttribute('src')) : '';
        const id = card.getAttribute('data-product-id') || title.toLowerCase().replace(/[^a-z0-9]/g, '');

        const cart = loadCart();
        if (!cart[id]) {
          cart[id] = { id, title, price, image, qty: 0 };
        }
        cart[id].qty = (cart[id].qty || 0) + 1;

        saveCart(cart);
        updateCartBadge();

        if (window.renderCart) window.renderCart();

        const originalText = newBtn.textContent;
        newBtn.textContent = 'Added ✓';
        newBtn.disabled = true;
        setTimeout(() => {
          newBtn.textContent = originalText;
          newBtn.disabled = false;
        }, 1000);
      });
    });
  }

  // --- 4. CART PAGE RENDERING ---
  function setupCartPage() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    if (!cartItems || !cartTotal) return;

    function renderCart() {
      const cart = loadCart();
      const items = Object.values(cart);

      if (items.length === 0) {
        cartItems.innerHTML = '<div class="box" style="text-align:center;"><p>Your cart is empty.</p><p><a href="produse.php" class="btn">Start Shopping</a></p></div>';
        cartTotal.textContent = '$0.00';
        return;
      }

      let total = 0;
      const html = items.map(item => {
        const price = parseFloat(item.price || 0);
        const itemTotal = price * (item.qty || 0);
        total += itemTotal;
        const imgUrl = item.image || 'https://via.placeholder.com/80?text=No+Img';

        return `
          <div class="box" style="margin-bottom: 1rem;">
            <div class="flex" style="justify-content: space-between; align-items: center;">
              <div class="flex" style="gap: 1.5rem; align-items: center; flex: 1;">
                <img src="${imgUrl}" alt="${item.title}" style="width: 80px; height: 80px; object-fit: contain; background: rgba(0,0,0,0.2); border-radius: 4px; border: 1px solid #444;">
                <div>
                  <h3 style="margin: 0 0 0.5rem 0; color: #FB8B24;">${item.title}</h3>
                  <p style="margin: 0; color: #D0CFEC;">$${price.toFixed(2)} x ${item.qty}</p>
                </div>
              </div>
              <div class="flex" style="gap: 1rem; align-items: center; justify-content: flex-end;">
                <p style="margin:0; font-weight:bold; font-size:1.1rem; min-width:80px; text-align:right;">$${itemTotal.toFixed(2)}</p>
                <div style="display:flex; align-items:center; gap:5px;">
                    <button class="btn" onclick="adjustQty('${item.id}', ${item.qty - 1})" style="padding: 0.4rem 0.8rem;">-</button>
                    <span style="display:inline-block; width:25px; text-align:center;">${item.qty}</span>
                    <button class="btn" onclick="adjustQty('${item.id}', ${item.qty + 1})" style="padding: 0.4rem 0.8rem;">+</button>
                </div>
                <button class="btn" onclick="removeFromCart('${item.id}')" style="background:#ff4444; color:white; margin-left:10px;">X</button>
              </div>
            </div>
          </div>`;
      }).join('');

      cartItems.innerHTML = html;
      cartTotal.textContent = `$${total.toFixed(2)}`;
    }

    window.removeFromCart = function (id) {
      const cart = loadCart();
      delete cart[id];
      saveCart(cart);
      updateCartBadge();
      renderCart();
    };

    window.adjustQty = function (id, newQty) {
      const cart = loadCart();
      if (newQty <= 0) delete cart[id];
      else cart[id].qty = newQty;
      saveCart(cart);
      updateCartBadge();
      renderCart();
    };

    window.renderCart = renderCart;
    renderCart();
  }

  // --- 5. INITIALIZATION ---
  document.addEventListener('DOMContentLoaded', () => {
    attachAddToCart();
    updateCartBadge();
    setupCartPage();

    // Define FRCart API
    window.FRCart = {
      get: loadCart,
      save: saveCart,
      clear() {
        localStorage.removeItem(CART_KEY);
        updateCartBadge();
        if (window.renderCart) window.renderCart();
      }
    };

    // Check for Success Message 
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.get('success') === '1') {
      window.FRCart.clear();
      window.history.replaceState({}, document.title, window.location.pathname);
    }

    // -- Search --
    const searchInput = qs('#sb');
    if (searchInput) {
      searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          window.location.href = 'produse.php?search=' + encodeURIComponent(searchInput.value);
        }
      });
      const searchBtn = qs('.search-btn') || qs('button', searchInput.parentElement);
      if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
          e.preventDefault();
          window.location.href = 'produse.php?search=' + encodeURIComponent(searchInput.value);
        });
      }
    }

    // -- Header Scroll --
    const header = qs('header');
    if (header) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 20) header.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
        else header.style.boxShadow = 'none';
      });
    }

    // -- Nav Highlight --
    const links = qsa('.nav-menu a');
    const current = window.location.pathname.split('/').pop() || 'pagina_home.php';
    links.forEach(a => {
      if (a.getAttribute('href') === current) a.classList.add('active');
    });

    // -- Filters --
    const form = document.getElementById('filter-form');
    const applyBtn = document.getElementById('apply_filters');
    if (form && applyBtn) {
      form.addEventListener('change', () => applyBtn.style.display = 'block');
    }

    const filterBtn = document.getElementById("filter_button");
    const productsAside = document.getElementById("products-aside");
    if (filterBtn && productsAside) {
      filterBtn.addEventListener("click", () => productsAside.classList.toggle("active"));
    }

    // -- Checkout Logic --
    const btnShowCheckout = document.getElementById('btn-show-checkout');
    const checkoutSection = document.getElementById('checkout-section');
    if (btnShowCheckout && checkoutSection) {
      btnShowCheckout.addEventListener('click', function () {
        const cart = window.FRCart.get();
        if (Object.keys(cart).length === 0) {
          alert("Your cart is empty!");
          return;
        }
        btnShowCheckout.style.display = 'none';
        checkoutSection.style.display = 'block';
        checkoutSection.scrollIntoView({ behavior: 'smooth' });
      });
    }

    const checkoutForm = document.getElementById('checkout-form');
    const cartInput = document.getElementById('cart_data_input');
    if (checkoutForm && cartInput) {
      checkoutForm.addEventListener('submit', function (e) {
        const cart = window.FRCart.get();
        if (Object.keys(cart).length === 0) {
          e.preventDefault();
          alert("Your cart is empty!");
          return;
        }
        cartInput.value = JSON.stringify(cart);
      });
    }
  });
})();
function fillAddress(data) {
  if (!data) return;

  // Populate fields
  document.getElementById('f_street').value = data.street || '';
  document.getElementById('f_city').value = data.city || '';
  document.getElementById('f_zip').value = data.zip_code || '';
  document.getElementById('f_country').value = data.country || 'Romania'; // Default to Romania if not found

  // If the saved address has a phone, use it, otherwise keep current
  if (data.phone_number) {
    document.getElementById('f_phone').value = data.phone_number;
  }

  // Visual feedback
  // Scroll slightly down to form
  document.getElementById('checkout-form').scrollIntoView({ behavior: 'smooth' });
}