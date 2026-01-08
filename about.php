<?php
session_start();
require 'db.php';
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

      <main>
        <div class="container" style="max-width: 50rem; margin: 2rem auto;">
          <div class="box" style="padding:2rem 2.5rem; text-align:center;">
            <h1 style="margin-bottom:1rem;">About FrameRate Parts</h1>
            <p class="lead" style="margin-bottom:2rem;">Empowering Your Digital Dreams with Premium PC Components</p>
            <h2 style="margin-top:2rem;">Our Story</h2>
            <p>Welcome to FrameRate Parts! We are dedicated to providing top-quality computer components to help you build the perfect PC. Our team is passionate about technology and committed to offering the best products and customer service in the industry.</p>
            <p>Whether you're a gamer, a professional, or a hobbyist, we have the parts you need to take your setup to the next level.</p>
          </div>
          <div class="cards-grid" style="margin:2rem 0;">
            <div class="card"><div class="value-icon">🎮</div><h3>Gaming Excellence</h3><p>Dedicated to providing the best components for ultimate gaming performance</p></div>
            <div class="card"><div class="value-icon">⚡</div><h3>Innovation</h3><p>Always at the forefront of technology with the latest PC components</p></div>
            <div class="card"><div class="value-icon">🛡️</div><h3>Quality Assurance</h3><p>Only the most reliable and trusted brands in our inventory</p></div>
            <div class="card"><div class="value-icon">💪</div><h3>Expert Support</h3><p>Technical expertise to help you make informed decisions</p></div>
          </div>
          <div class="services-section" style="margin:2rem 0;">
            <h2>Our Services</h2>
            <div class="cards-grid">
              <div class="card"><h3>Technical Consultation</h3><p>Our competent technical staff provides expert guidance for both assembly and product selection, ensuring you make the right choices for your build.</p></div>
              <div class="card"><h3>Custom PC Building</h3><p>Need help assembling your dream machine? Our experts can build it for you, ensuring optimal performance and reliability.</p></div>
              <div class="card"><h3>Product Testing</h3><p>All components undergo rigorous testing before shipping to ensure they meet our high-quality standards.</p></div>
            </div>
          </div>
          <div class="products-section" style="margin:2rem 0;">
            <h2>Our Products</h2>
            <div class="products-content">
              <p>We offer a comprehensive range of high-quality PC components:</p>
              <ul class="products-list">
                <li>High-performance graphics cards from leading manufacturers</li>
                <li>Latest generation processors for ultimate computing power</li>
                <li>Reliable motherboards for solid system foundation</li>
                <li>High-speed RAM for smooth multitasking</li>
                <li>Efficient cooling solutions for optimal performance</li>
                <li>Storage solutions for all your data needs</li>
              </ul>
            </div>
          </div>
        </div>
      </main>

      <aside>
        <div class="card">
          <h4>Quick Links</h4>
          <a href="produse.php">Products</a>
        </div>
      </aside>

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