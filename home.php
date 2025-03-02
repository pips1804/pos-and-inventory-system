<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/style/style.css">
</head>

<body class="m-5">
    <h1 class="text-center mb-3">Point of Sales System</h1>
    <div class="text-center mb-3">
        <a class="btn " href="home.php">Home</a>
        <button class="btn " onclick="loadPage('sales_report')">Sales Report</button>
        <button class="btn" onclick="loadPage('inventory_report')">Inventory Report</button>
        <button class="btn" onclick="loadPage('delivery_report')">Delivery Report</button>

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
    <script src="assets/scripts/reports.js"></script>
</body>

</html>
