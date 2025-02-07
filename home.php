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

    <div class="row">
        <div class="col-8 ">
            <h1 class="mb-3 sticky-title">Products</h1>
            <div class=" row row-cols-1 row-cols-md-4 g-2 products-container" style="max-height: 650px; overflow-y: scroll;">
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 d-flex flex-column" style="max-width: 300px;">
                            <img src="./assets/img/sample_img.jpg" class="card-img-top" alt="./assets/img/sample_img.jpg">
                            <div class="card-body d-flex flex-column">
                                <h4 class="card-title font-weight-bold"><?php echo $product['name']; ?></h4>
                                <p class="card-text"><?php echo $product['description']; ?></p>
                                <p class="card-text">$<?php echo $product['price']; ?></p>
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
                    foreach ($_SESSION['cart'] as $product_id => $item):
                        $product = $db->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
                        $subtotal = $item['quantity'] * $product['price'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo $product['name']; ?></b></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo $product['price']; ?></td>
                            <td>$<?php echo $subtotal; ?></td>
                            <td>
                                <button class="btn " onclick="showRemoveModal()">Remove</button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="fw-bold">Total</td>
                        <td class="fw-bold">$<?php echo $total; ?></td>
                        <td>
                            <form action="./controllers/checkout.php" method="post" onsubmit="disableCheckoutButton()">
                                <button type="submit" class="btn" id="checkoutButton">Checkout</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row align-middle">
                <a href="#" class="btn col-2" onclick="showLogoutModal()">Log out</a>
                <form action="./controllers/remove_cart.php" method="post" class="col-10">
                    <button type="submit" class="btn" id="removeCartButton">Remove All Items</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn " data-bs-dismiss="modal">Cancel</button>
                    <a href="controllers/logout.php" class="btn">Logout</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Remove Item Confirmation Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Removing of Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this item from the order list?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn " data-bs-dismiss="modal">Cancel</button>
                    <form action="./controllers/remove_from_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" class="btn">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Message Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="checkoutMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove All Cart Items Message Modal -->
    <div class="modal fade" id="removeCartModal" tabindex="-1" aria-labelledby="removeCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Remove all items from cart?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="removeCartMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var checkoutMessage = <?php echo isset($_SESSION['checkout_message']) ? json_encode($_SESSION['checkout_message']) : 'null'; ?>;
            if (checkoutMessage) {
                document.getElementById("checkoutMessage").innerText = checkoutMessage;
                var checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
                checkoutModal.show();
                <?php unset($_SESSION['checkout_message']); ?> // Clear the message after displaying
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            var removeCartMessage = <?php echo isset($_SESSION['remove_message']) ? json_encode($_SESSION['remove_message']) : 'null'; ?>;
            if (removeCartMessage) {
                document.getElementById("removeCartMessage").innerText = removeCartMessage;
                var removeCartModal = new bootstrap.Modal(document.getElementById('removeCartModal'));
                removeCartModal.show();
                <?php unset($_SESSION['remove_message']); ?> // Clear the message after displaying
            }
        });
    </script>


    <script src="./assets/scripts/scirpt.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
