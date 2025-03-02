<?php
include '../assets/scripts/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch product details from API
    $api_url = IMS_URL . "/api/products"; // Replace with your actual API URL
    $api_response = file_get_contents($api_url);

    if (!$api_response) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch product details.']);
        exit();
    }

    $products = json_decode($api_response, true);

    // Find the requested product in the API response
    $product = null;
    foreach ($products as $p) {
        if ($p['pid'] == $product_id) {
            $product = $p;
            break;
        }
    }

    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit();
    }

    $availableStock = intval($product['quantity']);
    $currentQuantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;

    // Check if there's enough stock
    if ($currentQuantity + $quantity > $availableStock) {
        echo json_encode(['status' => 'error', 'message' => 'Not enough stock available. Only ' . $availableStock . ' left.']);
        exit();
    }

    // Add product to cart session
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['pname'],
            'price' => $product['base_price'],
            'quantity' => $quantity
        ];
    }

    echo json_encode(['status' => 'success', 'message' => 'Product added to cart.']);
    exit();
}
