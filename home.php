<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/style/style.css">
</head>

<body class="m-5">
    <h1 class="text-center mb-3">Point of Sales System</h1>
    <div class="text-center mb-3">
        <a class="btn " href="home.php">Home</a>
        <button class="btn " onclick="loadPage('sales_report')">Sales Report</button>
        <button class="btn" onclick="loadPage('inventory_report')">Inventory Report</button>

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
    <div>
        <input type="file" id="qrCodeInput" accept="image/*">
        <button id="uploadQRButton">Confirm Delivery</button>
        <p id="deliveryStatus"></p>
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
                        console.log("✅ Sales report loaded.");
                        setTimeout(() => {
                            loadSalesChart();
                            attachPredictionEvent();
                        }, 300);
                    } else if (page === 'inventory_report') {
                        console.log("✅ Inventory report loaded.");
                        setTimeout(() => {
                            loadInventoryReport();
                        }, 300);
                    }
                })
                .catch(error => console.error('❌ Error loading page:', error));
        }

        function loadInventoryReport() {
            fetch('http://192.168.254.111:5000/api/inventory') // Replace with your actual API URL
                .then(response => response.json())
                .then(data => {
                    console.log("✅ Inventory Data Received:", data);
                    let inventoryTable = document.getElementById('inventoryData');
                    inventoryTable.innerHTML = '';

                    data.forEach((item, index) => {
                        inventoryTable.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.product_name}</td>
                            <td>${item.starting_inventory}</td>
                            <td>${item.inventory_received}</td>
                            <td>${item.inventory_shipped}</td>
                            <td><strong>${item.inventory_on_hand}</strong></td>
                        </tr>
                    `;
                    });
                })
                .catch(error => console.error('❌ Error fetching inventory data:', error));
        }

        document.addEventListener("DOMContentLoaded", function() {
            loadInventoryReport();
        });

        function attachPredictionEvent() {
            let predictBtn = document.getElementById('predictBtn');
            let monthsInput = document.getElementById('monthsInput');
            let output = document.getElementById('predictedSalesOutput');

            if (!predictBtn) {
                console.error("❌ Predict button not found!");
                return;
            }

            predictBtn.addEventListener('click', function() {
                let inputMonths = parseInt(monthsInput.value);
                console.log("📢 Predict Button Clicked with input:", inputMonths);

                if (!isNaN(inputMonths) && inputMonths > 0) {
                    fetch('./controllers/sales_data.php')
                        .then(response => response.json())
                        .then(data => {
                            console.log("✅ Sales Data for Prediction:", data);

                            const dates = data.map((_, i) => i + 1);
                            const sales = data.map(entry => parseFloat(entry.total_sales));

                            const {
                                m,
                                b
                            } = linearRegression(dates, sales);
                            console.log("📊 Regression Coefficients:", {
                                m,
                                b
                            });

                            let futureMonth = dates.length + inputMonths;
                            let predictedSales = m * futureMonth + b;
                            console.log(`📈 Predicted Sales for Month ${futureMonth}: ₱${predictedSales.toFixed(2)}`);

                            output.innerText = `Predicted Sales: ₱${predictedSales.toFixed(2)}`;
                        })
                        .catch(error => console.error('❌ Error fetching sales data:', error));
                } else {
                    console.log("⚠️ Invalid Input!");
                    output.innerText = "Please enter a valid number of months.";
                }
            });
        }

        function linearRegression(x, y) {
            let n = x.length;
            let sumX = x.reduce((a, b) => a + b, 0);
            let sumY = y.reduce((a, b) => a + b, 0);
            let sumXY = x.map((xi, i) => xi * y[i]).reduce((a, b) => a + b, 0);
            let sumXX = x.map(xi => xi * xi).reduce((a, b) => a + b, 0);

            let m = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
            let b = (sumY - m * sumX) / n;

            return {
                m,
                b
            };
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
