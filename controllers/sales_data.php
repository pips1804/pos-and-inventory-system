<?php
include '../db_connection/db_connect.php';

$query = "SELECT DATE(created_at) as date, SUM(total) as total_sales FROM orders GROUP BY DATE(created_at)";
$result = $db->query($query);

$sales_data = [];
while ($row = $result->fetch_assoc()) {

    $formatted_date = date("F j, Y", strtotime($row['date']));

    $sales_data[] = [
        'date' => $formatted_date,
        'total_sales' => $row['total_sales']
    ];
}

echo json_encode($sales_data);
