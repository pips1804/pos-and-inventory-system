<?php
session_start();

// Check if cart exists
if (empty($_SESSION['cart'])) {
    $_SESSION['remove_message'] = "Your cart is already empty.";
} else {
    $_SESSION['cart'] = []; // Clear the cart
    $_SESSION['remove_message'] = "Your cart has been emptied.";
}

// Redirect back to the home page or cart page
header("Location: ../home.php");
exit();
