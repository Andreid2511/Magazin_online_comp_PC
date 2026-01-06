<?php
session_start();
require 'db.php';

// Get Product ID from URL
$product_id = isset($_GET['product']) ? intval($_GET['product']) : 0;

// Fetch Product Details
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// Redirect if product not found
if (!$product) {
    header("Location: produse.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - FrameRate Parts</title>
    <link rel="stylesheet" href="./index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  </head>
  <body>
    <div class="page-grid">
      <header id="top">
        <div class="top_container">
          <div class="container">
            <a href="pagina_home.php"><h1 class="title">FrameRate Parts</h1></a>
          </div>
          <div class="container">
            <?php if(isset($_SESSION['user_name'])): ?>
              <a href="profil.php" class="social-link">Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></a>
            <?php else: ?>
              <a href="login.php" class="social-link">Contul meu</a>
            <?php endif; ?>
            <a href="cosul_meu.php" class="social-link">Cosul Meu</a>
          </div>
        </div>
        <nav class="nav-menu">
          <a href="pagina_home.php">Home</a>
          <a class="active" href="produse.php">Products</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </nav>
      </header>

      <div class="product-detail container" style="margin-top:2rem;">
        <div class="product-image">
          <img id="product-img" src="images/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="product-info">
          <h1 id="product-title"><?= htmlspecialchars($product['name']) ?></h1>
          
          <?php if($product['badge_label']): ?>
            <span class="product-badge" style="display:inline-block; margin-bottom:1rem;"><?= htmlspecialchars($product['badge_label']) ?></span>
          <?php endif; ?>

          <p class="lead"><?= htmlspecialchars($product['description']) ?></p>
          <p class="price" id="product-price">$<?= htmlspecialchars($product['price']) ?></p>
          
          <div class="card" style="border:none; background:transparent; padding:0; text-align:left;"
               data-product-id="<?= $product['product_id'] ?>"
               data-product-title="<?= htmlspecialchars($product['name']) ?>"
               data-product-price="<?= $product['price'] ?>"
               data-product-image="images/<?= htmlspecialchars($product['image_url']) ?>">
               
            <button class="add-to-cart btn" style="font-size:1.2rem; padding:1rem 2rem;">Add to Cart</button>
          </div>
          
          <p style="margin-top:1rem; color:#888;">Category: <?= htmlspecialchars($product['category_id']) ?> | Stock: <?= $product['stock_quantity'] ?></p>
        </div>
      </div>

      <footer>
        <div class="footer-bottom">
          <p>&copy; 2025 FrameRate Parts. All rights reserved.</p>
        </div>
      </footer>
    </div>
    <script src="index.js"></script>
  </body>
</html>