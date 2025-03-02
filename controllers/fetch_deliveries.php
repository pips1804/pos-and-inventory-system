<?php
include '../db_connection/db_connect.php';

$query = "SELECT * FROM deliver";
$result = $db->query($query);

$deliveries = [];
while ($row = $result->fetch_assoc()) {
    $deliveries[] = [
        "deliver_id" => $row["deliver_id"],
        "order_id" => $row["order_id"],
        "total" => $row["total"],
        "customer_id" => $row["customer_id"],
        "delivered" => $row["delivered"]
    ];
}

header('Content-Type: application/json');
echo json_encode($deliveries);
exit;
