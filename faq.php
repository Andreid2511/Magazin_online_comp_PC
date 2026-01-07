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
        <h1 class="title">FAQ</h1>
        <div class="box container">
          <p>Frequently asked questions will be listed here.</p>
        </div>
      </main>

      <aside>
        <div class="card">
          <h4>Help</h4>
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