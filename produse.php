<?php
// produsele.php - Updated for Clickable Cards & Smart Filters
session_start();
require 'db.php';

// 1. Build Query
$sql = "SELECT p.*, c.slug as category_slug FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";
$params = [];

// 2. Filter Logic
$has_active_filters = false; // Flag to decide if we show "Clear Filters"

if (!empty($_GET['search'])) {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
    $has_active_filters = true;
}

if (!empty($_GET['category'])) {
    $sql .= " AND c.slug = ?";
    $params[] = $_GET['category'];
    $has_active_filters = true;
}

if (!empty($_GET['price'])) {
    if ($_GET['price'] == 'under-100') {
        $sql .= " AND p.price < 100";
    } elseif ($_GET['price'] == '100-500') {
        $sql .= " AND p.price BETWEEN 100 AND 500";
    } elseif ($_GET['price'] == 'over-500') {
        $sql .= " AND p.price > 500";
    }
    // Only mark as active filter if it's not "all"
    if($_GET['price'] !== 'all') {
        $has_active_filters = true;
    }
}

// 3. Fetch Data
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - FrameRate Parts</title>
    <link rel="stylesheet" href="./index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  </head>
  <body>
    <div class="page-grid-products">
      <header id="top-products">
          <div class="top_container">
            <div class="container">
              <a href="pagina_home.php"><h1 class="title">FrameRate Parts</h1></a>
            </div>
            <div class="container">
              <form action="produse.php" method="GET" style="display:flex;">
                  <input name="search" id="sb" type="text" class="search-box" placeholder="Search..">
                  <button type="submit" class="search-btn">Cauta</button>
              </form>
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
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
            <a href="faq.html">FAQ</a>
          </nav>
      </header>

      <main id="products-main">
        <section class="featured-products">
          <h2>Our Products</h2>
          <button id="filter_button"><img src="images/filter.png"></button>
          
          <div class="product-list">
            <?php if (count($products) > 0): ?>
              <?php foreach ($products as $product): ?>
                
                <div class="card" 
                     data-product-id="<?= $product['product_id'] ?>" 
                     data-product-category="<?= htmlspecialchars($product['category_slug']) ?>">
                  
                  <a href="prezentare_produs.php?product=<?= $product['product_id'] ?>" class="card-link">
                      
                      <img src="images/<?= htmlspecialchars($product['image_url']) ?>" 
                           alt="<?= htmlspecialchars($product['name']) ?>" 
                           data-product-image="images/<?= htmlspecialchars($product['image_url']) ?>">
                      
                      <h3 data-product-title="<?= htmlspecialchars($product['name']) ?>">
                          <?= htmlspecialchars($product['name']) ?>
                      </h3>
                      
                      <p class="price" data-product-price="<?= $product['price'] ?>">
                          $<?= $product['price'] ?>
                      </p>
                  </a>
                  <button class="add-to-cart">Add to Cart</button>
                  
                </div>
                <?php endforeach; ?>
            <?php else: ?>
              <p>No products found matching your selection.</p>
            <?php endif; ?>
          </div>
        </section>
      </main>

      <aside id="products-aside">
        <div class="card card--muted filter-height">
          <h3>Filters</h3>
          
          <form action="produse.php" method="GET" id="filter-form">
            
            <?php if(!empty($_GET['search'])): ?>
                <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
            <?php endif; ?>

            <div class="filter-group">
              <h4>Category</h4>
              <div class="filter-list">
                <label class="filter-label">
                  All <input type="radio" name="category" value="" <?= empty($_GET['category']) ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  Processors <input type="radio" name="category" value="cpu" <?= (isset($_GET['category']) && $_GET['category'] == 'cpu') ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  Graphics Cards <input type="radio" name="category" value="gpu" <?= (isset($_GET['category']) && $_GET['category'] == 'gpu') ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  Memory <input type="radio" name="category" value="memory" <?= (isset($_GET['category']) && $_GET['category'] == 'memory') ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  Storage <input type="radio" name="category" value="storage" <?= (isset($_GET['category']) && $_GET['category'] == 'storage') ? 'checked' : '' ?>>
                </label>
              </div>
            </div>
            
            <hr style="margin:15px 0; border:0; border-top:1px solid #444;">
            
            <div class="filter-group">
              <h4>Price</h4>
              <div class="filter-list">
                <label class="filter-label">
                  Any Price <input type="radio" name="price" value="all" <?= (empty($_GET['price']) || $_GET['price'] == 'all') ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  Under $100 <input type="radio" name="price" value="under-100" <?= (isset($_GET['price']) && $_GET['price'] == 'under-100') ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  $100 - $500 <input type="radio" name="price" value="100-500" <?= (isset($_GET['price']) && $_GET['price'] == '100-500') ? 'checked' : '' ?>>
                </label>
                <label class="filter-label">
                  Over $500 <input type="radio" name="price" value="over-500" <?= (isset($_GET['price']) && $_GET['price'] == 'over-500') ? 'checked' : '' ?>>
                </label>
              </div>
            </div>

            <br>
            <button type="submit" id="apply_filters" class="btn">Apply Filters</button>

            <?php if($has_active_filters): ?>
                <a href="produse.php" class="btn-clear">Clear Filters</a>
            <?php endif; ?>
          </form>
        </div>
      </aside>

      <footer id="products-footer">
        <div class="footer-content">
          <div class="footer-section">
            <h4>Contact Us</h4>
            <p>Email: support@framerateshop.com</p>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2025 FrameRate Parts. All rights reserved.</p>
        </div>
      </footer>
      <a href="#top-products" class="back-to-top">⬆️ Top</a>
    </div>
    
    <script src="index.js"></script>

    <script>
        const form = document.getElementById('filter-form');
        const applyBtn = document.getElementById('apply_filters');
        
        // Listen for any change inside the form (clicking any radio button)
        form.addEventListener('change', function() {
            applyBtn.style.display = 'block'; // Reveal the button
        });
    </script>
  </body>
</html>