<?php
session_start();

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];

    // Check if cart exists and product is in cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); // Remove the item from cart
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Item not found in cart.']);
    }
    exit();
}
