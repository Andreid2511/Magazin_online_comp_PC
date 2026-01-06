// Basic site interactions: search redirect, cart (localStorage), nav highlighting, header scroll effect
(function () {
  'use strict';

  const CART_KEY = 'fr_cart_v1';

  // Helpers
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

  // Cart badge in header next to "Cosul Meu"
  function ensureCartBadge() {
    const cartLink = qs('a[href$="cosul_meu.html"]') || qs('a[href*="cosul_meu"]');
    if (!cartLink) return null;
    let badge = qs('#cart-count-badge', cartLink);
    if (!badge) {
      badge = document.createElement('span');
      badge.id = 'cart-count-badge';
      // Minimal inline style so we don't need CSS edits
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

  // Add to cart handler
  function attachAddToCart() {
    const buttons = qsa('button.add-to-cart, .add-to-cart');
    if (!buttons.length) return;
    buttons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        // Locate product card/container
        const card = btn.closest('.card') || btn.closest('.product-card') || btn.parentElement;
        if (!card) {
          console.warn('Add to cart: product container not found');
          return;
        }
        // Get product data from data attributes if available, fallback to DOM elements
        const titleEl = qs('[data-product-title]', card) || qs('h3', card) || qs('h2', card) || qs('h1', card);
        const priceEl = qs('[data-product-price]', card) || qs('.price', card);
        const imgEl = qs('[data-product-image]', card) || qs('img', card);
        
        const title = titleEl ? (titleEl.getAttribute('data-product-title') || titleEl.textContent.trim()) : 'Product';
        const price = priceEl ? (priceEl.getAttribute('data-product-price') || priceEl.textContent.replace(/[^0-9.,-]/g, '').trim()) : null;
        const image = imgEl ? (imgEl.getAttribute('data-product-image') || imgEl.getAttribute('src')) : null;
        const id = card.getAttribute('data-product-id') || title.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,'');

        const cart = loadCart();
        if (!cart[id]) cart[id] = { 
          id, 
          title, 
          price, 
          image,
          category: card.getAttribute('data-product-category') || 'default',
          qty: 0 
        };
        cart[id].qty = (cart[id].qty || 0) + 1;
        saveCart(cart);
        updateCartBadge();
        
        // If we're on the cart page, update the display
        if (window.renderCart) window.renderCart();

        // Simple feedback
        btn.textContent = 'Added ✓';
        btn.disabled = true;
        setTimeout(() => { btn.textContent = 'Add to Cart'; btn.disabled = false; }, 900);
      });
    });
  }

  // Simple search behavior: read #sb input and redirect to produse.html?search=...
  function attachSearch() {
    const input = qs('#sb');
    if (!input) return;
    // find a button in same container
    const container = input.closest('.container');
    const btn = container ? qs('button', container) : document.querySelector('button');

    function doSearch() {
      const q = (input.value || '').trim();
      if (!q) return;
      const url = 'produse.html?search=' + encodeURIComponent(q);
      window.location.href = url;
    }

    if (btn) btn.addEventListener('click', (e) => { e.preventDefault(); doSearch(); });
    input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });
  }

  // Highlight current nav link in .nav-menu
  function highlightNav() {
    const links = qsa('.nav-menu a');
    if (!links.length) return;
    const current = window.location.pathname.split('/').pop() || 'pagina_home.html';
    links.forEach(a => {
      // normalize
      const href = a.getAttribute('href') || '';
      const name = href.split('/').pop();
      if (!name) return;
      if (name === current || (current === '' && name === 'pagina_home.html')) {
        a.classList.add('active');
      } else {
        a.classList.remove('active');
      }
    });
  }

  // Header scroll effect: add subtle shadow to top_container when page scrolls
  function attachHeaderScroll() {
    const top = qs('.top_container') || qs('header');
    if (!top) return;
    function onScroll() {
      if (window.scrollY > 20) {
        top.style.boxShadow = '0 6px 16px rgba(0,0,0,0.12)';
      } else {
        top.style.boxShadow = 'none';
      }
    }
    window.addEventListener('scroll', onScroll);
    onScroll();
  }

  // Product page: swap product info based on ?product= param
  function swapProductInfo() {
    const params = new URLSearchParams(window.location.search);
    const product = params.get('product');
    if(product) {
      const data = {
        'product-a': {
          img: 'https://via.placeholder.com/300x300?text=Product+A',
          title: 'Sample Product A',
          price: '$49.99'
        },
        'product-b': {
          img: 'https://via.placeholder.com/300x300?text=Product+B',
          title: 'Sample Product B',
          price: '$89.99'
        },
        'product-c': {
          img: 'https://via.placeholder.com/300x300?text=Product+C',
          title: 'Sample Product C',
          price: '$129.99'
        }
      };
      if(data[product]) {
        var img = document.getElementById('product-img');
        var title = document.getElementById('product-title');
        var price = document.getElementById('product-price');
        if(img) img.src = data[product].img;
        if(title) title.textContent = data[product].title;
        if(price) price.textContent = data[product].price;
      }
    }
  }
  if(window.location.pathname.includes('prezentare_produs.html')) {
    swapProductInfo();
  }

  // Product list: link to prezentare_produs.html with correct product
  function setupProductLinks() {
    if(window.location.pathname.includes('produse.html')) {
      document.querySelectorAll('.product-list .card').forEach(function(card) {
        var id = card.getAttribute('data-product-id');
        if(id) {
          card.style.cursor = 'pointer';
          card.addEventListener('click', function(e) {
            // Only follow if not clicking a button
            if(e.target.tagName !== 'BUTTON') {
              window.location.href = 'prezentare_produs.html?product=' + id;
            }
          });
          // Also update Add to Cart button to link to product page
          var btn = card.querySelector('.add-to-cart');
          if(btn) {
            btn.addEventListener('click', function(e) {
              e.stopPropagation();
              window.location.href = 'prezentare_produs.html?product=' + id;
            });
          }
        }
      });
    }
  }
  setupProductLinks();

  // Cart page rendering
  function setupCartPage() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    if (!cartItems || !cartTotal) return; // Not on cart page

    function renderCart() {
      const cart = loadCart();
      const items = Object.values(cart);
      
      if (items.length === 0) {
        cartItems.innerHTML = '<div class="box"><p>Your cart is empty.</p><p><a href="produse.html">Continue shopping</a></p></div>';
        cartTotal.textContent = '$0.00';
        return;
      }

      let total = 0;
      const html = items.map(item => {
        const price = parseFloat(item.price || 0);
        const itemTotal = price * (item.qty || 0);
        total += itemTotal;
        
        return `
          <div class="box" style="margin-bottom: 1rem;">
            <div class="flex" style="justify-content: space-between; align-items: center;">
              <div class="flex" style="gap: 1rem; align-items: center;">
                ${item.image ? `<img src="${item.image}" alt="${item.title}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">` : ''}
                <div>
                  <h3>${item.title}</h3>
                  <p>Price: $${price.toFixed(2)} × ${item.qty} = $${itemTotal.toFixed(2)}</p>
                </div>
              </div>
              <div class="flex" style="gap: 1rem; align-items: center;">
                <button class="btn" onclick="adjustQty('${item.id}', ${item.qty - 1})">-</button>
                <span>${item.qty}</span>
                <button class="btn" onclick="adjustQty('${item.id}', ${item.qty + 1})">+</button>
                <button class="btn" onclick="removeFromCart('${item.id}')">Remove</button>
              </div>
            </div>
          </div>`;
      }).join('');
      
      cartItems.innerHTML = html;
      cartTotal.textContent = `$${total.toFixed(2)}`;
    }

    // Expose cart functions to window for button onclick handlers
    window.removeFromCart = function(id) {
      const cart = loadCart();
      delete cart[id];
      saveCart(cart);
      updateCartBadge();
      renderCart();
    };

    window.adjustQty = function(id, newQty) {
      const cart = loadCart();
      if (newQty <= 0) {
        delete cart[id];
      } else {
        cart[id].qty = newQty;
      }
      saveCart(cart);
      updateCartBadge();
      renderCart();
    };

    // Store render function globally so add-to-cart can trigger updates
    window.renderCart = renderCart;
    renderCart();
  }

  // Initialize everything
  document.addEventListener('DOMContentLoaded', () => {
    attachAddToCart();
    attachSearch();
    highlightNav();
    attachHeaderScroll();
    updateCartBadge();
    setupCartPage();

    // Expose a simple cart API on window for debugging in console
    window.FRCart = {
      get: loadCart,
      save: saveCart,
      clear() { 
        localStorage.removeItem(CART_KEY); 
        updateCartBadge();
        if (window.renderCart) window.renderCart();
      }
    };
  });
})();