<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

include '../db_connection/db_connect.php';

// Check database connection
if ($db->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $db->connect_error]));
}

$response = ["status" => "error", "message" => "Unknown error"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $total_amount = $_POST["total_amount"] ?? 0;
    $order_id = $_POST["order_id"];

    if (!is_numeric($total_amount) || $total_amount <= 0) {
        $response["message"] = "Invalid amount.";
        echo json_encode($response);
        exit;
    }

    // Start transaction
    $db->begin_transaction();

    try {
        // Insert into sales table
        $sqlSales = "INSERT INTO sales (sales_value) VALUES (?)";
        $stmtSales = $db->prepare($sqlSales);

        if (!$stmtSales) {
            throw new Exception("Prepare statement failed for sales: " . $db->error);
        }

        $stmtSales->bind_param("d", $total_amount);

        if (!$stmtSales->execute()) {
            throw new Exception("Failed to insert into sales: " . $stmtSales->error);
        }

        $sales_id = $stmtSales->insert_id; // Get last inserted ID
        $stmtSales->close();

        // Insert into deliver table
        $sqlDeliver = "INSERT INTO deliver (order_id, total, customer_id) VALUES (?, ?, 1)";
        $stmtDeliver = $db->prepare($sqlDeliver);

        if (!$stmtDeliver) {
            throw new Exception("Prepare statement failed for deliver: " . $db->error);
        }

        $stmtDeliver->bind_param("dd", $order_id, $total_amount);

        if (!$stmtDeliver->execute()) {
            throw new Exception("Failed to insert into deliver: " . $stmtDeliver->error);
        }

        $deliver_id = $stmtDeliver->insert_id; // Get last inserted ID
        $stmtDeliver->close();

        // Commit transaction
        $db->commit();

        $response = [
            "status" => "success",
            "message" => "Sale and delivery recorded successfully.",
            "sales_id" => $sales_id,
            "deliver_id" => $deliver_id
        ];
    } catch (Exception $e) {
        $db->rollback();
        $response["message"] = $e->getMessage();
    }
}

$db->close();
echo json_encode($response);
