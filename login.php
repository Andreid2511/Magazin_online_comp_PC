<?php
session_start();
require 'db.php';

$message = '';
$messageType = ''; // 'success' or 'error'

// 2. Handle Form Submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- HANDLE REGISTER ---
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $pass = $_POST['password'];
        $confirm = $_POST['confirm'];
        
        // Basic Validation
        if ($pass !== $confirm) {
            $message = "Passwords do not match!";
            $messageType = "error";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "This email is already registered.";
                $messageType = "error";
            } else {
                // ENCRYPT PASSWORD (The part you asked about)
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

                // Insert into Database
                $sql = "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $message = "Account created successfully! Please login.";
                    $messageType = "success";
                } else {
                    $message = "Error registering user.";
                    $messageType = "error";
                }
            }
        }
    }

    // --- HANDLE LOGIN ---
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = trim($_POST['email']);
        $pass = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify Password
        if ($user && password_verify($pass, $user['password_hash'])) {
            // Login Success: Save user info in Session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            
            // Redirect to Home
            header("Location: pagina_home.php");
            exit;
        } else {
            $message = "Invalid email or password.";
            $messageType = "error";
        }
    }
}

// --- HANDLE LOGOUT (if ?logout=true is in URL) ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
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
    <title>Login / Register - FrameRate Parts</title>
    <link rel="stylesheet" href="./index.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <style>
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .alert.error { background-color: #ffcccc; color: #cc0000; }
        .alert.success { background-color: #ccffcc; color: #006600; }
        .logged-in-box { text-align: center; padding: 4rem; }
    </style>
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
          <a href="pagina_home.php">Home</a>
          <a href="produse.php">Products</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <a href="faq.php">FAQ</a>
        </nav>
      </header>

      <main>
        <div class="container">
            
            <?php if ($message): ?>
                <div class="alert <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="box logged-in-box">
                    <h2>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
                    <p>You are currently logged in.</p>
                    <div style="margin-top: 2rem;">
                        <a href="profil.php" class="btn">View Profile</a>
                        <a href="login.php?logout=true" class="btn" style="background:#ff4444; color:white; margin-left:1rem;">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <div style="display: flex; gap: 2rem; flex-wrap: wrap; justify-content: center;">
                    
                    <div class="box" style="flex: 1; min-width: 300px;">
                        <h2>Customer Login</h2>
                        <form class="auth-form" action="login.php" method="POST">
                            <input type="hidden" name="action" value="login">
                            <div class="form-group">
                                <label for="login-email">Email:</label>
                                <input type="email" id="login-email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="login-password">Password:</label>
                                <input type="password" id="login-password" name="password" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn">Login</button>
                                <a href="#" class="forgot-password">Forgot Password?</a>
                            </div>
                        </form>
                    </div>

                    <div class="box" style="flex: 1; min-width: 300px;">
                        <h2>Register</h2>
                        <form class="auth-form" action="login.php" method="POST">
                            <input type="hidden" name="action" value="register">
                            <div class="form-group">
                                <label for="register-name">Name:</label>
                                <input type="text" id="register-name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="register-email">Email:</label>
                                <input type="email" id="register-email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="register-password">Password:</label>
                                <input type="password" id="register-password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="register-confirm">Confirm Password:</label>
                                <input type="password" id="register-confirm" name="confirm" required>
                            </div>
                            <div class="form-group terms-label">
                                <input type="checkbox" id="newsletter" name="newsletter">
                                <label for="newsletter">Subscribe to newsletter</label>
                            </div>
                            <div class="form-group terms-label">
                                <input type="checkbox" id="privacy" name="privacy" required>
                                <label for="privacy">I agree to Privacy Policy</label>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn">Register</button>
                            </div>
                        </form>
                    </div>
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
  </body>
</html>