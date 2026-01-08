<?php
session_start();
require 'db.php';

// SECURITY CHECK: If user is not logged in, redirect to Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// FETCH USER DETAILS
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
  //safety check
    echo "User not found.";
    exit;
}

// FETCH ORDER HISTORY
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderStmt->execute([$user_id]);
$orders = $orderStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FrameRate Parts</title>
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
        <div class="container" style="display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2rem;">
            
            <section class="profile-section box" style="flex: 1; min-width: 300px;">
                <h2 style="color: #FB8B24; border-bottom: 1px solid #444; padding-bottom: 0.5rem;">My Account</h2>
                
                <div style="margin-top: 1.5rem;">
                    <p><strong>Name:</strong> <br> <?= htmlspecialchars($user['full_name']) ?></p>
                    <p style="margin-top:1rem;"><strong>Email:</strong> <br> <?= htmlspecialchars($user['email']) ?></p>
                    <p style="margin-top:1rem;"><strong>Phone:</strong> <br> <?= htmlspecialchars($user['phone_number'] ?? 'Not provided') ?></p>
                    <p style="margin-top:1rem;"><strong>Member Since:</strong> <br> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
                </div>

                <div style="margin-top: 2rem;">
                    <a href="login.php?logout=true" class="btn" style="background-color: #ff4444; color: white;">Logout</a>
                </div>
            </section>

            <section class="box" style="flex: 2; min-width: 300px;">
                <h2 style="color: #FB8B24; border-bottom: 1px solid #444; padding-bottom: 0.5rem;">Order History</h2>
                
                <?php if (count($orders) > 0): ?>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <?php 
                                        // Simple badge logic
                                        $statusClass = 'badge-pending';
                                        if($order['status'] == 'completed') $statusClass = 'badge-completed';
                                        if($order['status'] == 'cancelled') $statusClass = 'badge-cancelled';
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="margin-top: 1.5rem; color: #888;">You haven't placed any orders yet.</p>
                    <a href="produse.php" class="btn" style="margin-top: 1rem; display:inline-block;">Start Shopping</a>
                <?php endif; ?>
            </section>

        </div>
      </main>

      <aside>
        <div class="card card--muted">
          <h4>Account Support</h4>
          <p>Need to update your details?</p>
          <a href="contact.php">Contact Support</a>
        </div>
      </aside>

      <footer>
        <div class="footer-content">
            <div class="footer-section">
            <h4>Contact Us</h4>
            <p>Email: support@framerateshop.com</p>
            </div>
            <div class="footer-section">
            <h4>Quick Links</h4>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
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