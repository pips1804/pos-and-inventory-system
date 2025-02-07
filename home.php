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
</head>

<body class="container mt-4">
    <h1 class="text-center mb-4">POS System</h1>

    <div class="row gx-5">
        <div class="col-8 ">
            <h2 class="mb-3 sticky-title">Products</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4 products-container" style="max-height: 700px; overflow-y: scroll;">
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text"><?php echo $product['description']; ?></p>
                                <p class="card-text">$<?php echo $product['price']; ?></p>
                                <p class="card-text">Stock: <?php echo $product['stock']; ?></p>

                                <!-- Push form to bottom in all cards -->
                                <div class="mt-auto d-flex align-items-center">
                                    <form action="./controllers/add_to_cart.php" method="post" class="d-flex w-100">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
                                            class="form-control me-2" style="width: 80px;">
                                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>

        <div class="col-4  gx-5">
            <h2 class="mt-4 mb-4">Shopping Cart</h2>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
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
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo $product['price']; ?></td>
                            <td>$<?php echo $subtotal; ?></td>
                            <td>
                                <form action="./controllers/remove_from_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="fw-bold">Total</td>
                        <td class="fw-bold">$<?php echo $total; ?></td>
                        <td>
                            <form action="./controllers/checkout.php" method="post">
                                <button type="submit" class="btn btn-success">Checkout</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
