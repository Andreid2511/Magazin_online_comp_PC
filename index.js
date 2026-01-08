(function () {
  'use strict';

  const CART_KEY = 'fr_cart_v1';

  // ---HELPER FUNCTIONS---
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

  // ---BADGE LOGIC---
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

  // ---ADD TO CART LOGIC---
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

        let title = card.getAttribute('data-product-title');
        if (!title) {
          const el = qs('[data-product-title]', card) || qs('h3', card);
          title = el ? (el.getAttribute('data-product-title') || el.textContent.trim()) : 'Product';
        }

        let price = card.getAttribute('data-product-price');
        if (!price) {
          const el = qs('[data-product-price]', card) || qs('.price', card);
          price = el ? (el.getAttribute('data-product-price') || el.textContent.replace(/[^0-9.,-]/g, '').trim()) : '0';
        }

        let image = card.getAttribute('data-product-image');
        if (!image) {
          const el = qs('[data-product-image]', card) || qs('img', card);
          image = el ? (el.getAttribute('data-product-image') || el.getAttribute('src')) : '';
        }

        let id = card.getAttribute('data-product-id');
        // Fallback ID generation if missing
        if (!id) id = title.toLowerCase().replace(/[^a-z0-9]/g, '');

        const cart = loadCart();
        if (!cart[id]) {
          cart[id] = { id, title, price, image, qty: 0 };
        }
        cart[id].qty = (cart[id].qty || 0) + 1;

        saveCart(cart);
        updateCartBadge();

        if (window.renderCart) window.renderCart();

        // Feedback Animation
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

  // ---CART PAGE RENDERING---
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
              
              <a href="prezentare_produs.php?product=${item.id}" style="display: flex; gap: 1.5rem; align-items: center; flex: 1; text-decoration: none; color: inherit;">
                <img src="${imgUrl}" alt="${item.title}" style="width: 80px; height: 80px; object-fit: contain; background: rgba(0,0,0,0.2); border-radius: 4px; border: 1px solid #444;">
                <div>
                  <h3 style="margin: 0 0 0.5rem 0; color: #FB8B24;">${item.title}</h3>
                  <p style="margin: 0; color: #D0CFEC;">$${price.toFixed(2)} x ${item.qty}</p>
                </div>
              </a>

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

  // ---INITIALIZATION---
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
  document.getElementById('f_country').value = data.country || 'Romania';

  if (data.phone_number) {
    document.getElementById('f_phone').value = data.phone_number;
  }

  document.getElementById('checkout-form').scrollIntoView({ behavior: 'smooth' });
}

/*-- Accordion Logic --*/
document.addEventListener('DOMContentLoaded', function () {
  const headers = document.querySelectorAll('.accordion-header');

  headers.forEach(header => {
    header.addEventListener('click', () => {
      const isActive = header.classList.contains('active');

      headers.forEach(h => {
        h.classList.remove('active');
        h.nextElementSibling.style.maxHeight = null;
      });

      if (!isActive) {
        header.classList.add('active');
        const content = header.nextElementSibling;
        content.style.maxHeight = content.scrollHeight + "px";
      }
    });
  });
});

// Wishlist Form Submission
const wishlistForm = document.getElementById('wishlist-form');
if (wishlistForm) {
  wishlistForm.addEventListener('submit', function (e) {
    const cart = FRCart.get();
    if (Object.keys(cart).length === 0) {
      e.preventDefault();
      alert("Your cart is empty! Add items before saving a build.");
      return;
    }
    document.getElementById('wishlist_cart_input').value = JSON.stringify(cart);
  });
}

/* Enlarge Product Image Modal */
document.addEventListener('DOMContentLoaded', function () {
  const productImg = document.getElementById('product-img');

  if (productImg) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';

    const modalImg = document.createElement('img');
    modalImg.className = 'image-modal-content';
    modalImg.alt = "Enlarged view";

    modal.appendChild(modalImg);
    document.body.appendChild(modal);

    productImg.addEventListener('click', function () {
      modalImg.src = this.src;
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    });

    const closeModal = () => {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    };

    modal.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        closeModal();
      }
    });
  }
});