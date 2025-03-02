<?php
header('Content-Type: application/json');
include '../db_connection/db_connect.php'; // Include your DB connection

$sql = "SELECT
            p.pname AS product_name,
            p.quantity AS starting_inventory,
            COALESCE(SUM(purchase.quantity), 0) AS inventory_received,
            COALESCE(SUM(o.total_shipped), 0) AS inventory_shipped,
            (p.quantity + COALESCE(SUM(purchase.quantity), 0) - COALESCE(SUM(o.total_shipped), 0)) AS inventory_on_hand
        FROM ims_product p
        LEFT JOIN ims_purchase purchase ON p.pid = purchase.product_id
        LEFT JOIN ims_order o ON p.pid = o.product_id
        GROUP BY p.pid, p.pname, p.quantity";

$result = $conn->query($sql);
$inventory = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
}

echo json_encode($inventory);
