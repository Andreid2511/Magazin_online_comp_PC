<?php
session_start();
require 'db.php';

$msg = "";
$msgClass = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $subject, $message])) {
                $msg = "Message sent successfully! We will contact you soon.";
                $msgClass = "success";
            } else {
                $msg = "Error sending message. Please try again.";
                $msgClass = "error";
            }
        } catch (PDOException $e) {
            $msg = "Database Error: " . $e->getMessage();
            $msgClass = "error";
        }
    } else {
        $msg = "Please fill in all required fields.";
        $msgClass = "error";
    }
}
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
    <title>Proiect_TW - Contact</title>
    <link rel="stylesheet" href="./index.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
  </head>
  <body>
    <div class="page-grid">
      <header id="top">
        <div class = "top_container">
          <div class="container">
            <a href="pagina_home.php"><h1 class="title">FrameRate Parts</h1></a>
          </div>
          <div class="container">
            <input name="searchbox" id="sb" type="text" class="search-box" placeholder="Search..">
            <button class="search-btn">Cauta</button>
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
          <a href="produse.php">Products</a>
          <a href="about.php">About</a>
          <a class="active" href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </nav>
      </header>

      <main>
        <div class="container" style="max-width: 40rem; margin: 2rem auto;">
          <div class="box" style="padding:2rem 2.5rem;">
            <h1 style="text-align:center; margin-bottom:1.5rem;">Contact Us</h1>
            
            <?php if($msg): ?>
                <div class="alert <?= $msgClass ?>"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <form class="contact-form" action="contact.php" method="POST" style="margin-bottom:2rem;">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
              </div>
              <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
              </div>
              <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
              </div>
              <button type="submit" class="btn" style="width:100%;margin-top:1rem;">Send Message</button>
            </form>

            <hr style="margin:2rem 0;">
            
            <div class="contact-details" style="display:flex; flex-direction:column; gap:1rem; align-items:center;">
              <div class="contact-item"><strong>Email:</strong> support@framerateshop.com</div>
              <div class="contact-item"><strong>Phone:</strong> +40 123 456 789</div>
              <div class="contact-item"><strong>Address:</strong> 123 Gaming Street, Bucharest, Romania</div>
            </div>
            
            <div class="social-links" style="margin:2rem 0; text-align:center;">
              <a href="https://facebook.com/framerateparts" class="social-link">Facebook</a>
              <a href="https://twitter.com/framerateparts" class="social-link">Twitter</a>
              <a href="https://instagram.com/framerateparts" class="social-link">Instagram</a>
            </div>
            
            <div class="map-placeholder" style="margin-top:1rem; text-align:center;">
              <img src="https://via.placeholder.com/600x300" alt="Location Map" style="max-width:100%; border-radius:.5rem; box-shadow:0 2px 10px rgba(0,0,0,.08);">
            </div>
          </div>
        </div>
      </main>

      <aside>
        <div class="card">
          <h4>Support</h4>
          <p>Phone: +40 123 456 789</p>
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