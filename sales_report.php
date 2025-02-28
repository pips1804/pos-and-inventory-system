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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('./controllers/sales_data.php')
            .then(response => response.json())
            .then(data => {
                console.log("Sales Data Received:", data); // Debugging

                if (!data || data.length === 0) {
                    console.error('No sales data available');
                    return;
                }

                setTimeout(() => { // Delay to ensure canvas is fully loaded
                    const canvas = document.getElementById('salesChart');
                    if (!canvas) {
                        console.error("Error: 'salesChart' element not found!");
                        return;
                    }

                    const ctx = canvas.getContext('2d');

                    // Destroy previous chart instance if it exists
                    if (window.salesChartInstance) {
                        window.salesChartInstance.destroy();
                    }

                    const dates = data.map(entry => entry.date);
                    const sales = data.map(entry => parseFloat(entry.total_sales)); // Ensure numbers

                    console.log("Dates:", dates);
                    console.log("Sales:", sales);

                    window.salesChartInstance = new Chart(ctx, {
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

                    console.log("Chart successfully rendered!");

                }, 300); // Small delay
            })
            .catch(error => console.error('Error fetching sales data:', error));
    });
    console.log(typeof Chart);
</script>
