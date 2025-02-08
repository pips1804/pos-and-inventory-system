<?php
include '../db_connection/db_connect.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM products WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
$products = $db->query($query);

if ($products->num_rows > 0) {
    while ($product = $products->fetch_assoc()): ?>
        <div class="col">
            <div class="card h-100 d-flex flex-column" style="max-width: 300px;">
                <img src="./assets/img/sample_img.jpg" class="card-img-top" alt="Product Image">
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title font-weight-bold"><?php echo $product['name']; ?></h4>
                    <p class="card-text"><?php echo $product['description']; ?></p>
                    <p class="card-text">₱<?php echo $product['price']; ?></p>
                    <p class="card-text">Stock: <?php echo $product['stock']; ?></p>
                    <div class="mt-auto d-flex align-items-center">
                        <form action="./controllers/add_to_cart.php" method="post" class="d-flex w-100">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" style="width: 80px; background-color: #3A4750;" max="<?php echo $product['stock']; ?>"
                                class="form-control me-2" style="width: 80px;">
                            <button type="submit" class="btn ">Add to Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php endwhile;
} else {
    echo "<p class='text-center text-danger'>No products found</p>";
}
?>
