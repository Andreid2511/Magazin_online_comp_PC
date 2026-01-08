<?php
session_start();
require 'db.php';

// Specific Featured Products
$featured_names = [
    'GeForce RTX 5090',          
    'AMD Ryzen 9 7950X',          
    '32GB DDR5 RAM Kit',          
    'MSI MAG B650 Tomahawk', 
    'Samsung 990 PRO 2TB',        
    'Fractal Design North'        
];

// Create placeholders for the SQL IN clause (?,?,...)
$placeholders = implode(',', array_fill(0, count($featured_names), '?'));

$sql = "SELECT p.*, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.name IN ($placeholders)
        ORDER BY FIELD(p.name, " . implode(',', array_fill(0, count($featured_names), '?')) . ")";

// Twice where and order by, so merge params
$params = array_merge($featured_names, $featured_names);

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$featured_products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Proiect_TW</title>
    <link rel="stylesheet" href="./index.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
  </head>
  <body>
    <div class="page-grid">
      <header id="top">
        <div class="top_container">
          
          <div class="header-col-logo">
            <a href="pagina_home.php"><h1 class="title">FrameRate Parts</h1></a>
          </div>
          
          <div class="header-col-search">
            <form action="produse.php" method="GET">
                <input name="search" id="sb" type="text" class="search-box" placeholder="Search..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="search-btn">Cauta</button>
            </form>
          </div>

          <div class="header-col-user">
            <div class="user-menu">
              <?php if(isset($_SESSION['user_name'])): ?>
                
                <a href="wishlist.php" class="header-btn">
                  <span class="icon">♥</span> 
                  <span>Wishlist</span>
                </a>

                <a href="profil.php" class="header-btn">
                  <span class="icon">👤</span> 
                  <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </a>

              <?php else: ?>
                <a href="login.php" class="header-btn">
                  <span class="icon">👤</span> 
                  <span>Login</span>
                </a>
              <?php endif; ?>
              
              <a href="cosul_meu.php" class="header-btn">
                <span class="icon">🛒</span> 
                <span>Cart</span>
              </a>
            </div>
          </div>
        </div>

        <nav class="nav-menu">
          <a href="pagina_home.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pagina_home.php' ? 'active' : '' ?>">Home</a>
          <a href="produse.php" class="<?= basename($_SERVER['PHP_SELF']) == 'produse.php' ? 'active' : '' ?>">Products</a>
          <a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">About</a>
          <a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Contact</a>
          <a href="faq.php" class="<?= basename($_SERVER['PHP_SELF']) == 'faq.php' ? 'active' : '' ?>">FAQ</a>
        </nav>
      </header>

        <nav class="nav-menu">
          <a href="pagina_home.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pagina_home.php' ? 'active' : '' ?>">Home</a>
          <a href="produse.php" class="<?= basename($_SERVER['PHP_SELF']) == 'produse.php' ? 'active' : '' ?>">Products</a>
          <a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">About</a>
          <a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Contact</a>
          <a href="faq.php" class="<?= basename($_SERVER['PHP_SELF']) == 'faq.php' ? 'active' : '' ?>">FAQ</a>
        </nav>
      </header>

      <main>
      <section class="hero">
        <div class="hero-content">
          <h1>Build Your Dream PC</h1>
          <p>High-performance components for gaming and professional use</p>
          <a href="produse.php" class="cta-button">Shop Now</a>
        </div>
      </section>

      <section class="categories">
        <h2>Shop by Category</h2>
        <div class="cards-grid">
          <div class="card">
            <img src="images/cpu_category.jpg" alt="Processors">
            <h3>Processors</h3>
            <a href="produse.php?category=cpu" class="category-link">View All</a>
          </div>
          <div class="card">
            <img src="images/gpu_category.jpg" alt="Graphics Cards">
            <h3>Graphics Cards</h3>
            <a href="produse.php?category=gpu" class="category-link">View All</a>
          </div>
          <div class="card">
            <img src="images/ram_category.jpg" alt="Memory">
            <h3>Memory</h3>
            <a href="produse.php?category=memory" class="category-link">View All</a>
          </div>
          <div class="card">
            <img src="images/storage_category.jpg" alt="Storage">
            <h3>Storage</h3>
            <a href="produse.php?category=storage" class="category-link">View All</a>
          </div>
        </div>
      </section>

      <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="product-list">
          
          <?php foreach ($featured_products as $product): ?>
            <div class="card" 
                 data-product-id="<?= $product['product_id'] ?>" 
                 data-product-category="<?= htmlspecialchars($product['category_slug']) ?>">
              
              <?php if(!empty($product['badge_label'])): ?>
                  <div class="product-badge"><?= htmlspecialchars($product['badge_label']) ?></div>
              <?php endif; ?>

              <a href="prezentare_produs.php?product=<?= $product['product_id'] ?>" class="card-link">
                  <?php $img = !empty($product['image_url']) ? "images/" . htmlspecialchars($product['image_url']) : "https://via.placeholder.com/200"; ?>
                  <img src="<?= $img ?>" 
                       alt="<?= htmlspecialchars($product['name']) ?>" 
                       data-product-image="<?= $img ?>">
                  
                  <h3 data-product-title="<?= htmlspecialchars($product['name']) ?>">
                      <?= htmlspecialchars($product['name']) ?>
                  </h3>
                  
                  <p class="price" data-product-price="<?= $product['price'] ?>">
                      $<?= number_format($product['price'], 2) ?>
                  </p>
              </a>

              <button class="add-to-cart">Add to Cart</button>
            </div>
          <?php endforeach; ?>

        </div>
      </section>

      <section class="features">
        <h2>Why Choose Us</h2>
        <div class="cards-grid">
          <div class="card">
            <i class="feature-icon">🚚</i>
            <h3>Fast Shipping</h3>
            <p>Free shipping on orders over $100</p>
          </div>
          <div class="card">
            <i class="feature-icon">⚡</i>
            <h3>Expert Support</h3>
            <p>24/7 technical assistance</p>
          </div>
          <div class="card">
            <i class="feature-icon">🛡️</i>
            <h3>Warranty</h3>
            <p>3-year warranty on all products</p>
          </div>
          <div class="card">
            <i class="feature-icon">💰</i>
            <h3>Best Prices</h3>
            <p>Price match guarantee</p>
          </div>
        </div>
      </section>
      </main>

      <footer>
      <div class="footer-content">
        <div class="footer-section">
          <h4>Contact Us</h4>
          <p>Email: support@framerateshop.com</p>
          <p>Phone: (555) 123-4567</p>
        </div>
        <div class="footer-section">
          <h4>Quick Links</h4>
          <a href="about.php">About Us</a>
          <a href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </div>
        <div class="footer-section">
          <h4>Follow Us</h4>
          <div class="social-links">
            <a href="#" class="social-link">Facebook</a>
            <a href="#" class="social-link">Twitter</a>
            <a href="#" class="social-link">Instagram</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 FrameRate Parts. All rights reserved.</p>
      </div>
      </footer>

      <a href="#top" class="back-to-top">⬆️ Top</a>
    </div>
    <script src="index.js"></script>
  </body>
</html>