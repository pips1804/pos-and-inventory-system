<?php
include './db_connection/db_connect.php';

$query = "SELECT DATE(created_at) as date, SUM(total) as total_sales FROM orders GROUP BY DATE(created_at)";
$result = $db->query($query);

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = $row;
}
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #00adb5;">
            <h4 class="mb-0 text-center">Sales Report</h4>
        </div>
        <div class="card-body">
            <div class="text-center">
                <canvas id="salesChart" style="max-width: 500px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('./controllers/sales_data.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    console.error('No sales data available');
                    return;
                }

                const dates = data.map(entry => entry.date);
                const sales = data.map(entry => entry.total_sales);

                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Total Sales (₱)',
                            data: sales,
                            backgroundColor: '#3a4750',
                            borderColor: '#eeeeee',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching sales data:', error));
    });
</script>
