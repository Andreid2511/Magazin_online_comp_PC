<?php
session_start();
require 'db.php';

// Check if we just placed an order successfully
$order_success = isset($_GET['success']) && $_GET['success'] == 1;

$user_phone = '';
$saved_addresses = [];

if (isset($_SESSION['user_id'])) {
    // 1. Fetch User Phone
    $stmt = $pdo->prepare("SELECT phone_number FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $u = $stmt->fetch();
    if($u) {
        $user_phone = $u['phone_number'] ?? ''; 
    }

    // 2. Fetch Saved Addresses
    $sql = "SELECT country, city, street, zip_code, phone_number 
            FROM addresses 
            WHERE user_id = ? 
            GROUP BY country, city, street, zip_code, phone_number 
            ORDER BY MAX(address_id) DESC 
            LIMIT 3";
            
    $stmtAddr = $pdo->prepare($sql);
    $stmtAddr->execute([$_SESSION['user_id']]);
    $saved_addresses = $stmtAddr->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Shopping Cart - FrameRate Parts</title>
    <link rel="stylesheet" href="./index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  </head>
  <body>
    <div class="page-grid">
      <header id="top">
        <div class = "top_container">
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
          <a class="active" href="pagina_home.php">Home</a>
          <a href="produse.php">Products</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </nav>
      </header>

      <main>
        <div class="container">
          <h1>Your Shopping Cart</h1>

          <?php if($order_success): ?>
            <div class="alert-success">
                Order placed successfully! Thank you for shopping with us.
            </div>
          <?php endif; ?>
          
          <div id="cart-items"></div>
          
          <div id="cart-summary" class="box" style="margin-top: 2rem;">
            
            <div class="flex" style="justify-content: space-between; align-items: center;">
              <h3>Total: <span id="cart-total">$0.00</span></h3>
              <div>
                  <button class="btn" onclick="FRCart.clear()" style="background:#ff4444; color:white;">Clear Cart</button>
                  
                  <?php if(isset($_SESSION['user_id'])): ?>
                      <button id="btn-show-checkout" class="btn" style="background:#00c851; color:white; margin-left:10px;">Proceed to Checkout</button>
                  <?php else: ?>
                      <a href="login.php" class="btn" style="background:#FB8B24; margin-left:10px;">Login to Checkout</a>
                  <?php endif; ?>
              </div>
            </div>

            <div id="checkout-section">
                
                <?php if(!empty($saved_addresses)): ?>
                    <h3 style="color:#FB8B24; margin-bottom:1rem; border-top:1px solid #444; padding-top:1rem;">Saved Addresses</h3>
                    <p style="font-size:0.9rem; color:#aaa; margin-bottom:1rem;">Click a card to auto-fill the form below.</p>
                    
                    <div class="saved-addresses-grid">
                        <?php foreach($saved_addresses as $addr): ?>
                            <div class="address-card" 
                                 onclick='fillAddress(<?= json_encode($addr) ?>)'>
                                <h4><?= htmlspecialchars($addr['city']) ?></h4>
                                <p><?= htmlspecialchars($addr['street']) ?></p>
                                <p><?= htmlspecialchars($addr['zip_code']) ?>, <?= htmlspecialchars($addr['country']) ?></p>
                                <button class="btn use-btn">Use This</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3 style="color:#FB8B24; margin-bottom:1rem; border-top:1px solid #444; padding-top:1rem;">Delivery Details</h3>
                
                <form id="checkout-form" action="place_order.php" method="POST">
                    <input type="hidden" name="cart_data" id="cart_data_input">
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="f_phone" required 
                               value="<?= htmlspecialchars($user_phone) ?>" 
                               placeholder="07xx xxx xxx">
                    </div>

                    <div class="checkout-grid">
                        <div class="form-group">
                            <label>Country</label>
                            <select name="country" id="f_country" required style="width:100%; padding:.6rem; border-radius:.25rem; background:#291720; color:#D0CFEC; border:1px solid #D0CFEC;">
                                <option value="Austria">Austria</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Greece">Greece</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Italy">Italy</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Malta">Malta</option>
                                <option value="Netherlands">Netherlands</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Romania">Romania</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Spain">Spain</option>
                                <option value="Sweden">Sweden</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" id="f_city" required 
                                   pattern="(?=.*[a-zA-Z]).+" 
                                   title="City must contain letters"
                                   placeholder="Bucharest">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Street Address</label>
                        <input type="text" name="street" id="f_street" required 
                               pattern="(?=.*[a-zA-Z]).+"
                               title="Street must contain letters"
                               placeholder="Str. Victoriei, Nr. 2">
                    </div>
                    
                    <div class="form-group">
                        <label>Zip Code</label>
                        <input type="text" name="zip" id="f_zip" required 
                               pattern="\d+" 
                               title="Zip code must be numbers only"
                               placeholder="010101">
                    </div>

                    <div class="checkout-actions">
                        <button type="submit" class="btn checkout-btn">Send Order</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </main>

      <footer>
        <div class="footer-bottom">
          <p>&copy; 2025 FrameRate Parts. All rights reserved.</p>
        </div>
      </footer>
    </div>
    <script src="index.js"></script>
  </body>
</html>