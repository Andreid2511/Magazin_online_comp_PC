<?php
session_start();
require 'db.php';

//Security Check
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pagina_home.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_json = $_POST['cart_data'];
$cart = json_decode($cart_json, true);

if (empty($cart)) {
    die("Error: Cart is empty.");
}

$pdo->beginTransaction();

try {
    // Update User Phone (Keep profile updated)
    $stmt = $pdo->prepare("UPDATE users SET phone_number = ? WHERE user_id = ?");
    $stmt->execute([$_POST['phone'], $user_id]);

    // Save Address for this specific order
    $stmt = $pdo->prepare("INSERT INTO addresses (user_id, phone_number, country, city, street, zip_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id, 
        $_POST['phone'], 
        $_POST['country'], 
        $_POST['city'], 
        $_POST['street'], 
        $_POST['zip']
    ]);
    $address_id = $pdo->lastInsertId();

    // Create Order
    $total_amount = 0;
    foreach ($cart as $item) {
        $total_amount += $item['price'] * $item['qty'];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, address_id, total_amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $address_id, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Insert Order Items
    $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
    $stmt_item = $pdo->prepare($sql_item);

    foreach ($cart as $item) {
        $prod_id = intval($item['id']);
        $stmt_item->execute([$order_id, $prod_id, $item['qty'], $item['price']]);
    }

    $pdo->commit();

    // REDIRECT BACK TO CART WITH SUCCESS FLAG
    header("Location: cosul_meu.php?success=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Order Failed: " . $e->getMessage());
}
?>