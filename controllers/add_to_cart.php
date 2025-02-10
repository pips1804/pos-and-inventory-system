<?php
session_start();
include('../db_connection/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);


    $query = $db->query("SELECT * FROM products WHERE id = $product_id");
    $product = $query->fetch_assoc();

    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit();
    }

    $availableStock = intval($product['stock']);
    $currentQuantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;

    if ($currentQuantity + $quantity > $availableStock) {
        echo json_encode(['status' => 'error', 'message' => 'Not enough stock available. Only ' . $availableStock . ' left.']);
        exit();
    }


    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = ['quantity' => $quantity];
    }

    echo json_encode(['status' => 'success']);
    exit();
}
