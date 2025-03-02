<?php
include '../db_connection/db_connect.php';

$query = "SELECT DATE_FORMAT(sale_date, '%Y-%m') AS month,
                 SUM(sales_value) AS total_sales
          FROM sales
          WHERE sale_date IS NOT NULL
          GROUP BY YEAR(sale_date), MONTH(sale_date)
          ORDER BY sale_date ASC";

$result = $db->query($query);

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = [
        "month" => $row["month"], // Now stores 'YYYY-MM'
        "total_sales" => floatval($row["total_sales"]) // Convert to number
    ];
}

header('Content-Type: application/json');
echo json_encode($sales_data);
exit;
