<?php
include('./db_connection/db_connect.php');
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$products = $db->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Reenie+Beanie&display=swap" rel="stylesheet">
</head>

<body class="m-5">
    <p class="text-center title">Point of Sales System</p>

    <div class="d-flex justify-content-center">
        <form class="w-50 d-flex">
            <div class="input-group">
                <input type="text" id="searchBox" class="form-control" placeholder="Search products..." style="background-color: #3A4750;">
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-8 ">
            <h1 class="mb-3 sticky-title">Products</h1>
            <div class=" row row-cols-1 row-cols-md-4 g-2 products-container" style="max-height: 460px; overflow-y: scroll;">
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 d-flex flex-column" style="max-width: 300px;">
                            <img src="./assets/img/sample_img.jpg" class="card-img-top" alt="./assets/img/sample_img.jpg">
                            <div class="card-body d-flex flex-column">
                                <h4 class="card-title font-weight-bold"><?php echo $product['name']; ?></h4>
                                <p class="card-text d-inline-block text-truncate" style="cursor: pointer;" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($product['description']); ?>">
                                    <?php echo $product['description']; ?>
                                </p>
                                <p class="card-text">₱<?php echo $product['price']; ?></p>
                                <p class="card-text">Stock: <?php echo $product['stock']; ?></p>

                                <div class="mt-auto d-flex align-items-center">
                                    <form action="./controllers/add_to_cart.php" method="post" class="d-flex w-100">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
                                            class="form-control me-2" style="width: 80px; background-color: #3A4750;">
                                        <button type="submit" class="btn ">Add to Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-4  gx-5">
            <h1 class="mt-4 mb-4">Order List</h1>
            <table class="table table-bordered table-dark">
                <thead class="table-head">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $taxRate = .12;
                    foreach ($_SESSION['cart'] as $product_id => $item):
                        $product = $db->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
                        $subtotal = $item['quantity'] * $product['price'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo $product['name']; ?></b></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₱<?php echo $product['price']; ?></td>
                            <td>₱<?php echo $subtotal; ?></td>
                            <td>
                                <button class="btn " onclick="showRemoveModal()">Remove</button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="fw-bold">Total with tax (₱<?php echo $total * $taxRate; ?>)</td>
                        <td class="fw-bold">₱<?php echo $total * $taxRate + $total; ?></td>
                        <td>
                            <form action="./controllers/checkout.php" method="post" onsubmit="disableCheckoutButton()">
                                <button type="submit" class="btn" id="checkoutButton">Checkout</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row align-items-center">
                <a href="#" class="btn w-auto me-2" onclick="showLogoutModal()">Log out</a>
                <button onclick="showRemoveAllModal()" class="btn w-auto" id="removeCartButton">Remove All Items</button>
            </div>

        </div>
    </div>

    <?php
    include_once('./modals.php');
    ?>

    <script src="./assets/scripts/scirpt.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
