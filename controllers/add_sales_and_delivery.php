<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

include '../db_connection/db_connect.php'; // Ensure correct path

$response = ["status" => "error", "message" => "Unknown error"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $total_amount = $_POST["total_amount"] ?? 0;

    if (!is_numeric($total_amount) || $total_amount <= 0) {
        $response["message"] = "Invalid amount.";
        echo json_encode($response);
        exit;
    }

    // Start a transaction to ensure both inserts succeed
    $db->begin_transaction();

    try {
        // Insert into sales table
        $sqlSales = "INSERT INTO sales (sales_value) VALUES (?)";
        $stmtSales = $db->prepare($sqlSales);
        $stmtSales->bind_param("d", $total_amount);
        
        if (!$stmtSales->execute()) {
            throw new Exception("Failed to insert into sales: " . $stmtSales->error);
        }

        $stmtSales->close();

        // Insert into deliver table (auto-increment deliver_id, customer_id always 1)
        $sqlDeliver = "INSERT INTO deliver (total, customer_id) VALUES (?, 1)";
        $stmtDeliver = $db->prepare($sqlDeliver);
        $stmtDeliver->bind_param("d", $total_amount);

        if (!$stmtDeliver->execute()) {
            throw new Exception("Failed to insert into deliver: " . $stmtDeliver->error);
        }

        $stmtDeliver->close();

        // Commit transaction
        $db->commit();
        
        $response = ["status" => "success", "message" => "Sale and delivery recorded successfully."];
    } catch (Exception $e) {
        $db->rollback(); // Rollback on failure
        $response["message"] = $e->getMessage();
    }
}

$db->close();
echo json_encode($response);
?>
