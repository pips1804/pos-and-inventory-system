<?php
session_start();
include('../db_connection/db_connect.php');

header("Content-Type: application/json");

// Check if cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode([]);
    exit();
}

$cart = [];
foreach ($_SESSION['cart'] as $product_id => $item) {
    // Fetch product details from API
    $apiUrl = "http://192.168.100.30:5000/api/products"; // Adjust API URL if needed
    $response = file_get_contents($apiUrl);
    $products = json_decode($response, true);

    // Find the product in the API response
    $product = array_filter($products, fn($p) => $p['pid'] == $product_id);
    if (!empty($product)) {
        $product = array_values($product)[0]; // Get first match

        $cart[] = [
            "id" => $product['pid'],
            "name" => $product['pname'],
            "price" => $product['base_price'],
            "quantity" => $item['quantity']
        ];
    }
}

// Return cart as JSON
echo json_encode($cart);
exit();
