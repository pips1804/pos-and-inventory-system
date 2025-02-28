<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="m-5">
    <h1 class="text-center mb-3">Point of Sales System</h1>
    <div class="text-center mb-3">
        <a class="btn " href="home.php">Home</a>
        <button class="btn " onclick="loadPage('sales_report')">Sales Report</button>
    </div>

    <div id="content">
        <div class="d-flex justify-content-center">
            <form class="w-50 d-flex">
                <div class="input-group">
                    <input type="text" id="searchBox" class="form-control" placeholder="Search products...">
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-8">
                <h1 class="mb-3">Products</h1>
                <div class="row row-cols-1 row-cols-md-4 g-2 products-container" style="max-height: 580px; overflow-y: scroll;">
                    <!-- Products will be loaded dynamically -->
                </div>
            </div>

            <div class="col-4">
                <h1 class="mt-4 mb-4">Order List</h1>
                <table class="table table-bordered table-dark">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        <!-- Cart items will be loaded here dynamically -->
                    </tbody>
                </table>
                <button id="checkoutButton" class="btn btn-primary">Checkout</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/scripts/script.js"></script>
    <script>
        function loadPage(page) {
            fetch(page + '.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('content').innerHTML = html;
                    if (page === 'sales_report') {
                        console.log("Sales report loaded. Initializing chart...");
                        loadSalesChart(); // Manually initialize the chart
                    }
                })
                .catch(error => console.error('Error loading page:', error));
        }

        function loadSalesChart() {
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
        }


        // Function to reattach search functionality
        function attachSearchFunctionality() {
            let searchBox = document.getElementById("searchBox");
            if (!searchBox) return; // Prevent errors if search box is not present

            searchBox.addEventListener("keyup", function() {
                let searchQuery = this.value.trim();

                let xhr = new XMLHttpRequest();
                xhr.open(
                    "GET",
                    "./controllers/search_item.php?search=" + encodeURIComponent(searchQuery),
                    true
                );

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.querySelector(".products-container").innerHTML = xhr.responseText;
                    }
                };

                xhr.send();
            });
        }

        // Reattach search function when the page loads
        document.addEventListener("DOMContentLoaded", function() {
            attachSearchFunctionality();
        });
    </script>
</body>

</html>
