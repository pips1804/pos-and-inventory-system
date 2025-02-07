<?php
session_start();

$db = new mysqli('localhost', 'root', '', 'pos_system');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    $_SESSION['checkout_message'] = "Your cart is empty. Please add items before checking out.";
    header("Location: ../home.php"); // Redirect back to main page
    exit();
}

$total = 0;
foreach ($_SESSION['cart'] as $product_id => $item) {
    $product = $db->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
    $total += $item['quantity'] * $product['price'];
}

$db->query("INSERT INTO orders (total) VALUES ($total)");
$order_id = $db->insert_id;

foreach ($_SESSION['cart'] as $product_id => $item) {
    $product = $db->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
    $db->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, {$item['quantity']}, {$product['price']})");
    $db->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = $product_id");
}

// Clear cart
$_SESSION['cart'] = [];
$_SESSION['checkout_message'] = "Checkout successful! Your order ID is: $order_id";

header("Location: ../home.php"); // Redirect back to main page
exit();
