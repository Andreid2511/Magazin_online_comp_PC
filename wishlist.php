<?php
session_start();
require 'db.php';

// Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Delete Request
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM wishlists WHERE wishlist_id = ? AND user_id = ?");
    $stmt->execute([$_POST['delete_id'], $_SESSION['user_id']]);
    header("Location: wishlist.php?deleted=1");
    exit;
}

// Fetch Wishlists & Their Items
$stmt = $pdo->prepare("
    SELECT w.wishlist_id, w.title, w.notes, w.created_at,
           p.product_id, p.name as product_name, p.price, p.image_url, wi.quantity
    FROM wishlists w
    LEFT JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id
    LEFT JOIN products p ON wi.product_id = p.product_id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group data by Wishlist ID
$wishlists = [];
foreach ($raw_data as $row) {
    $id = $row['wishlist_id'];
    if (!isset($wishlists[$id])) {
        $wishlists[$id] = [
            'title' => $row['title'],
            'notes' => $row['notes'],
            'date'  => $row['created_at'],
            'items' => [],
            'total' => 0
        ];
    }
    // Add item if it exists (check for null product_id in case of empty wishlist)
    if ($row['product_id']) {
        $wishlists[$id]['items'][] = [
            'id'    => $row['product_id'],
            'title' => $row['product_name'],
            'price' => $row['price'],
            'image' => $row['image_url'], // JS cart logic
            'qty'   => $row['quantity']
        ];
        $wishlists[$id]['total'] += ($row['price'] * $row['quantity']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>My Wishlists - FrameRate Parts</title>
    <link rel="stylesheet" href="./index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
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
          <a href="pagina_home.php">Home</a>
          <a href="produse.php">Products</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </nav>
      </header>

      <main>
        <div class="container" style="max-width: 800px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
                <h1 class="title" style="margin:0;">My Saved Builds</h1>
                <a href="produse.php" class="btn">Create New Build</a>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert success">Build saved successfully! Cart cleared.</div>
            <?php endif; ?>
            <?php if(isset($_GET['deleted'])): ?>
                <div class="alert success">Build deleted.</div>
            <?php endif; ?>

            <?php if(empty($wishlists)): ?>
                <div class="box" style="text-align:center;">
                    <p>You haven't saved any builds yet.</p>
                    <p>Go to your <a href="cosul_meu.php" style="color:#FB8B24;">Cart</a> to save your current selection.</p>
                </div>
            <?php else: ?>
                
                <div class="accordion">
                    <?php foreach($wishlists as $wid => $w): ?>
                        <div class="accordion-item">
                            <button class="accordion-header">
                                <div class="w-info">
                                    <span style="font-size:1.1rem;"><?= htmlspecialchars($w['title']) ?></span>
                                    <span class="w-meta-header"><?= count($w['items']) ?> items • Saved: <?= date('M d, Y', strtotime($w['date'])) ?></span>
                                </div>
                                <div class="w-price-header">
                                    $<?= number_format($w['total'], 2) ?>
                                </div>
                            </button>

                            <div class="accordion-content">
                                <div class="wishlist-content-inner">
                                    
                                    <?php if(!empty($w['notes'])): ?>
                                        <div class="wishlist-note">
                                            "<?= htmlspecialchars($w['notes']) ?>"
                                        </div>
                                    <?php endif; ?>

                                    <ul class="w-item-list">
                                        <?php foreach($w['items'] as $item): ?>
                                            <li>
                                                <span class="w-item-name">
                                                    <span class="w-item-qty"><?= $item['qty'] ?>x</span> 
                                                    <?= htmlspecialchars($item['title']) ?>
                                                </span>
                                                <span class="w-item-price">$<?= number_format($item['price'] * $item['qty'], 2) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="w-actions">
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this build?');">
                                            <input type="hidden" name="delete_id" value="<?= $wid ?>">
                                            <button type="submit" class="btn" style="background:#ff4444; color:white; padding:0.5rem 1rem; font-size:0.9rem;">Delete</button>
                                        </form>

                                        <button class="btn" onclick='loadToCart(<?= json_encode($w['items']) ?>)' style="background:#00c851; color:white; font-weight:bold;">
                                            Load to Cart 🛒
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>
      </main>

      <footer>
        <div class="footer-bottom">
          <p>&copy; 2025 FrameRate Parts. All rights reserved.</p>
        </div>
      </footer>
    </div>
    
    <script src="index.js"></script>
    <script>
        // Function to load items into the cart
        function loadToCart(items) {
            if(!confirm("This will replace your current cart. Continue?")) return;

            // 1. Clear current cart
            if(window.FRCart) {
                window.FRCart.clear();
            }

            // 2. Build new cart object
            const newCart = {};
            items.forEach(item => {
                // Ensure image path is correct if we need it for the cart display later
                let imgPath = item.image;
                if(imgPath && !imgPath.startsWith('images/') && !imgPath.startsWith('http')) {
                    imgPath = 'images/' + imgPath;
                }

                newCart[item.id] = {
                    id: item.id,
                    title: item.title,
                    price: item.price,
                    image: imgPath,
                    qty: item.qty
                };
            });

            // 3. Save to localStorage
            window.FRCart.save(newCart);
            
            // 4. Redirect to cart
            window.location.href = 'cosul_meu.php';
        }
    </script>
  </body>
</html>