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
    <title>Proiect_TW - FAQ</title>
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
        <div class="container">
            <h1 class="title" style="text-align:center; margin-bottom:2rem;">Frequently Asked Questions</h1>
            
            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-header">
                        1. What is the advantage of buying from FrameRate Parts?
                    </button>
                    <div class="accordion-content">
                        <p>We treat every customer equally! Whether you are buying a single cable or a $5000 workstation, we offer premium support, fast shipping, and a dedicated team of experts ready to help you build your dream PC.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        2. How long does shipping take?
                    </button>
                    <div class="accordion-content">
                        <p>For items in stock, we typically process orders within 24 hours. Standard delivery within Romania takes 1-2 business days. International shipping times vary depending on the destination, usually between 3-7 business days.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        3. Do you offer warranty on your products?
                    </button>
                    <div class="accordion-content">
                        <p>Yes, absolutely. All new products sold by FrameRate Parts come with a standard manufacturer warranty, which is usually between 2 to 3 years. We handle the warranty process for you to make it as hassle-free as possible.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        4. Can I return a product if I change my mind?
                    </button>
                    <div class="accordion-content">
                        <p>Yes, you can return products within 14 days of receipt, provided they are in their original packaging and condition. Please contact our support team to initiate a return authorization (RMA).</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        5. Do you offer technical support for building PCs?
                    </button>
                    <div class="accordion-content">
                        <p>We sure do! If you bought your components from us, you can contact our technical department for advice on assembly or compatibility. We want to ensure your build boots up on the first try!</p>
                    </div>
                </div>
            </div>
            
        </div>
      </main>

      <aside>
        <div class="card">
          <h4>Help Center</h4>
          <p>Need more help?</p>
          <a href="contact.php" class="btn" style="display:block; margin-top:0.5rem; text-align:center;">Contact Support</a>
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