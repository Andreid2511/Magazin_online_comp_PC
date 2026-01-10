<?php
// produsele.php - Updated for Sorting, Pagination & Matrix Grid
session_start();
require 'db.php';

// --- 1. INITIALIZE PARAMETERS ---
$params = [];
$where_clauses = ["1=1"]; // Default true condition

// --- 2. BUILD FILTERS ---

// Search
if (!empty($_GET['search'])) {
    $where_clauses[] = "p.name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

// Category
if (!empty($_GET['category'])) {
    $where_clauses[] = "c.slug = ?";
    $params[] = $_GET['category'];
}

// Price
if (!empty($_GET['price'])) {
    if ($_GET['price'] == 'under-100') {
        $where_clauses[] = "p.price < 100";
    } elseif ($_GET['price'] == '100-500') {
        $where_clauses[] = "p.price BETWEEN 100 AND 500";
    } elseif ($_GET['price'] == 'over-500') {
        $where_clauses[] = "p.price > 500";
    }
}

// --- 3. SORTING LOGIC ---
$sort_option = $_GET['sort'] ?? 'featured';
$order_sql = "ORDER BY p.product_id DESC"; // Default (Newest/Featured)

if ($sort_option == 'price_asc') {
    $order_sql = "ORDER BY p.price ASC";
} elseif ($sort_option == 'price_desc') {
    $order_sql = "ORDER BY p.price DESC";
}

// --- 4. PAGINATION LOGIC ---
$limit = 40; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Combine WHERE clauses
$where_sql = implode(" AND ", $where_clauses);

// A. Get Total Count (For Pagination)
$count_sql = "SELECT COUNT(*) FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE $where_sql";
try {
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_items = $stmt->fetchColumn();
    $total_pages = ceil($total_items / $limit);
} catch (PDOException $e) {
    echo "Error counting products: " . $e->getMessage();
    exit;
}

// B. Get Actual Products
$sql = "SELECT p.*, c.slug as category_slug FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE $where_sql 
        $order_sql 
        LIMIT $limit OFFSET $offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
    exit;
}

// Helper to keep URL params when clicking pages
function get_url($new_page = null) {
    $query = $_GET;
    if ($new_page !== null) {
        $query['page'] = $new_page;
    }
    return '?' . http_build_query($query);
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

      <main id="products-main">
        <section class="featured-products">
            
          <div class="products-header">
              <h2>Our Products</h2>
              
              <form action="produse.php" method="GET" class="sort-form">
                  <?php foreach($_GET as $key => $val): ?>
                      <?php if($key != 'sort' && $key != 'page'): ?>
                          <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($val) ?>">
                      <?php endif; ?>
                  <?php endforeach; ?>

                  <label for="sort" style="margin-right: 10px;">Order by:</label>
                  <select name="sort" id="sort" onchange="this.form.submit()">
                      <option value="featured" <?= $sort_option == 'featured' ? 'selected' : '' ?>>Featured</option>
                      <option value="price_asc" <?= $sort_option == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                      <option value="price_desc" <?= $sort_option == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                  </select>
              </form>
          </div>
          
          <button id="filter_button"><img src="images/filter.png"></button>
          
          <div class="product-list">
            <?php if (count($products) > 0): ?>
              <?php foreach ($products as $product): ?>
                
                <div class="card" 
                     data-product-id="<?= $product['product_id'] ?>" 
                     data-product-category="<?= htmlspecialchars($product['category_slug']) ?>">
                  
                  <a href="prezentare_produs.php?product=<?= $product['product_id'] ?>" class="card-link">
                      
                      <?php $img = !empty($product['image_url']) ? "images/".htmlspecialchars($product['image_url']) : "https://via.placeholder.com/200"; ?>
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
            <?php else: ?>
              <p style="grid-column: 1/-1; text-align:center;">No products found matching your selection.</p>
            <?php endif; ?>
          </div>

          <?php if ($total_pages > 1): ?>
          <div class="pagination">
              <?php if ($page > 1): ?>
                  <a href="<?= get_url($page - 1) ?>">&laquo; Prev</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                  <a href="<?= get_url($i) ?>" class="<?= $i == $page ? 'active' : '' ?>">
                      <?= $i ?>
                  </a>
              <?php endfor; ?>

              <?php if ($page < $total_pages): ?>
                  <a href="<?= get_url($page + 1) ?>">Next &raquo;</a>
              <?php endif; ?>
          </div>
          <?php endif; ?>

        </section>
      </main>

      <aside id="products-aside">
        <div class="card card--muted filter-height">
          <button id="close_filters" class="close-filters-btn" aria-label="Close Filters">✕</button>
          <h3>Filters</h3>
          
          <form action="produse.php" method="GET" id="filter-form">
            <?php if(isset($_GET['sort'])): ?>
                <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
            <?php endif; ?>
            
            <?php if(!empty($_GET['search'])): ?>
                <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
            <?php endif; ?>

            <div class="filter-group">
              <h4>Category</h4>
              <div class="filter-list">
                <label class="filter-label">All <input type="radio" name="category" value="" <?= empty($_GET['category']) ? 'checked' : '' ?>></label>
                <label class="filter-label">Processors <input type="radio" name="category" value="cpu" <?= (isset($_GET['category']) && $_GET['category'] == 'cpu') ? 'checked' : '' ?>></label>
                <label class="filter-label">Graphics Cards <input type="radio" name="category" value="gpu" <?= (isset($_GET['category']) && $_GET['category'] == 'gpu') ? 'checked' : '' ?>></label>
                <label class="filter-label">Memory <input type="radio" name="category" value="memory" <?= (isset($_GET['category']) && $_GET['category'] == 'memory') ? 'checked' : '' ?>></label>
                <label class="filter-label">Storage <input type="radio" name="category" value="storage" <?= (isset($_GET['category']) && $_GET['category'] == 'storage') ? 'checked' : '' ?>></label>
                <label class="filter-label">Motherboards <input type="radio" name="category" value="motherboard" <?= (isset($_GET['category']) && $_GET['category'] == 'motherboard') ? 'checked' : '' ?>></label>
                <label class="filter-label">Power Supplies <input type="radio" name="category" value="psu" <?= (isset($_GET['category']) && $_GET['category'] == 'psu') ? 'checked' : '' ?>></label>
                <label class="filter-label">PC Cases <input type="radio" name="category" value="case" <?= (isset($_GET['category']) && $_GET['category'] == 'case') ? 'checked' : '' ?>></label>
                <label class="filter-label">Cooling <input type="radio" name="category" value="cooler" <?= (isset($_GET['category']) && $_GET['category'] == 'cooler') ? 'checked' : '' ?>></label>
              </div>
            </div>
            
            <div class="filter-group">
              <h4>Price</h4>
              <div class="filter-list">
                <label class="filter-label">Any Price <input type="radio" name="price" value="all" <?= (empty($_GET['price']) || $_GET['price'] == 'all') ? 'checked' : '' ?>></label>
                <label class="filter-label">Under $100 <input type="radio" name="price" value="under-100" <?= (isset($_GET['price']) && $_GET['price'] == 'under-100') ? 'checked' : '' ?>></label>
                <label class="filter-label">$100 - $500 <input type="radio" name="price" value="100-500" <?= (isset($_GET['price']) && $_GET['price'] == '100-500') ? 'checked' : '' ?>></label>
                <label class="filter-label">Over $500 <input type="radio" name="price" value="over-500" <?= (isset($_GET['price']) && $_GET['price'] == 'over-500') ? 'checked' : '' ?>></label>
              </div>
            </div>

            <br>
            <button type="submit" id="apply_filters" class="btn">Apply Filters</button>

            <?php 
               // Check if we have active filters to show Clear button
               $active = !empty($_GET['category']) || !empty($_GET['search']) || (isset($_GET['price']) && $_GET['price'] != 'all');
               if($active): 
            ?>
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

  </body>
</html>