<?php
session_start();

if (isset($_SESSION["cart"])) {
    unset($_SESSION["cart"]); // Remove cart session data
}

echo json_encode(["status" => "success", "message" => "Cart cleared successfully."]);
