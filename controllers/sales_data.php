<?php
include '../db_connection/db_connect.php';

$query = "SELECT DATE(date) as date, SUM(sales_value) as total_sales FROM sales GROUP BY DATE(date)";
$result = $db->query($query);

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = [
        "date" => $row["date"],
        "total_sales" => floatval($row["total_sales"]) // Convert to number
    ];
}

header('Content-Type: application/json');
echo json_encode($sales_data);
exit;
