<?php
session_start();
require 'db.php';

// Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Final save logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_save'])) {
    $title = trim($_POST['title']);
    $notes = trim($_POST['notes']);
    $cart_json = $_POST['cart_data'];
    $cart = json_decode($cart_json, true);

    if (!empty($cart) && !empty($title)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, title, notes) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $notes]);
            $wishlist_id = $pdo->lastInsertId();

            $stmt_item = $pdo->prepare("INSERT INTO wishlist_items (wishlist_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($cart as $item) {
                $stmt_item->execute([$wishlist_id, $item['id'], $item['qty']]);
            }

            $pdo->commit();
            header("Location: wishlist.php?success=1");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error saving build: " . $e->getMessage();
        }
    } else {
        $error = "Please provide a title and ensure cart is not empty.";
    }
}

$cart_data_input = $_POST['cart_data'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Save Wishlist - FrameRate Parts</title>
    <link rel="stylesheet" href="./index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-grid">
        <header id="top">
            <div class="top_container">
                <div class="header-col-logo"><a href="pagina_home.php"><h1 class="title">FrameRate Parts</h1></a></div>
                <div class="header-col-search">
                    <form action="produse.php" method="GET">
                        <input name="search" id="sb" type="text" class="search-box" placeholder="Search...">
                        <button type="submit" class="search-btn">Cauta</button>
                    </form>
                </div>
                <div class="header-col-user">
                    <div class="user-menu">
                         <a href="profil.php" class="header-btn"><span class="icon">👤</span><span><?= htmlspecialchars($_SESSION['user_name']) ?></span></a>
                         <a href="cosul_meu.php" class="header-btn"><span class="icon">🛒</span><span>Cart</span></a>
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
            <div class="container" style="max-width: 40rem; margin: 2rem auto;">
                <div class="box">
                    <h1 style="text-align:center; color:#FB8B24; margin-bottom:1.5rem;">Name Your Build</h1>
                    <p style="text-align:center; margin-bottom:2rem;">Save your current cart configuration to your wishlist for later.</p>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert error"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="save_wishlist.php" method="POST">
                        <input type="hidden" name="cart_data" value="<?= htmlspecialchars($cart_data_input) ?>">
                        <input type="hidden" name="confirm_save" value="1">

                        <div class="form-group">
                            <label>Build Title (e.g., "Dream Gaming PC")</label>
                            <input type="text" name="title" required placeholder="My Awesome Build">
                        </div>

                        <div class="form-group">
                            <label>Notes / Comments</label>
                            <textarea name="notes" rows="4" placeholder="Waiting for RTX 5090 release..."></textarea>
                        </div>

                        <div class="form-actions" style="margin-top:2rem;">
                            <a href="cosul_meu.php" class="btn" style="background:#444;">Cancel</a>
                            <button type="submit" class="btn" style="background:#FB8B24; font-weight:bold;">Save to Wishlist</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
        <footer>
            <div class="footer-bottom"><p>&copy; 2025 FrameRate Parts. All rights reserved.</p></div>
        </footer>
    </div>
    <script src="index.js"></script>
</body>
</html>