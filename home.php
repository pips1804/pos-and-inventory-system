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
        <button class="btn " onclick="loadPage('./sales_report')">Sales Report</button>
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
    <script>
document.addEventListener("DOMContentLoaded", function() {
    fetchProducts();
    loadCart();
});

function fetchProducts() {
    fetch("http://192.168.100.11:5000/api/products")
        .then(response => response.json())
        .then(data => {
            const productsContainer = document.querySelector(".products-container");
            productsContainer.innerHTML = ""; // Clear existing products

            data.forEach(product => {
                let productCard = `
                    <div class="col">
                        <div class="card h-100 d-flex flex-column">
                            <img src="./assets/img/pou.png" class="card-img-top" alt="Product Image">
                            <div class="card-body d-flex flex-column">
                                <h4 class="card-title">${product.pname}</h4>
                                <p class="card-text">${product.description}</p>
                                <p class="card-text">₱${product.base_price}</p>
                                <p class="card-text stock" data-id="${product.pid}">Stock: ${product.quantity}</p>
                                <button class="btn btn-success add-to-cart"
                                    data-id="${product.pid}"
                                    data-name="${product.pname}"
                                    data-price="${product.base_price}"
                                    data-stock="${product.quantity}">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                productsContainer.innerHTML += productCard;
            });

            attachAddToCartEvent(); // Reattach event listeners after updating products
        })
        .catch(error => console.error("Error fetching products:", error));
}

setInterval(fetchProducts, 1000);



function attachAddToCartEvent() {
    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function() {
            const productId = this.getAttribute("data-id"); // Match API pid
            const productName = this.getAttribute("data-name");
            const productPrice = this.getAttribute("data-price");
            const maxStock = parseInt(this.getAttribute("data-stock"), 10);
            const quantity = 1; // Default to 1 for now

            if (maxStock < quantity) {
                alert("Not enough stock available!");
                return;
            }

            fetch("./controllers/add_to_cart.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "error") {
                    alert(data.message);
                } else {
                    alert("Added to cart successfully!");
                    loadCart(); // Refresh the cart
                }
            })
            .catch(error => console.error("Error adding to cart:", error));
        });
    });
}


function loadCart() {
    fetch("./controllers/get_cart.php")
        .then(response => response.json())
        .then(cart => {
            const cartItems = document.getElementById("cart-items");
            cartItems.innerHTML = "";

            let totalAmount = 0;

            cart.forEach(item => {
                let itemTotal = item.quantity * item.price;
                totalAmount += itemTotal;

                let cartRow = `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.quantity}</td>
                        <td>₱${item.price}</td>
                        <td>₱${itemTotal}</td>
                        <td>
                            <button class="btn btn-danger btn-sm remove-from-cart" data-id="${item.id}">Remove</button>
                        </td>
                    </tr>
                `;
                cartItems.innerHTML += cartRow;
            });

            attachRemoveCartEvent();
        })
        .catch(error => console.error("Error loading cart:", error));
}

function attachRemoveCartEvent() {
    document.querySelectorAll(".remove-from-cart").forEach(button => {
        button.addEventListener("click", function() {
            const productId = this.getAttribute("data-id");

            fetch("./controllers/remove_from_cart.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    loadCart();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error removing from cart:", error));
        });
    });
}

document.getElementById("checkoutButton").addEventListener("click", function() {
    fetch("./controllers/get_cart.php")
        .then(response => response.json())
        .then(cart => {
            fetch("http://192.168.100.11:5000/api/update_stock", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ cart })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // Clear the cart after a successful stock update
                    fetch("./controllers/clear_cart.php", {
                        method: "POST"
                    })
                    .then(() => {
                        alert("Checkout successful!");
                        loadCart(); // Reload cart to reflect changes
                    })
                    .catch(error => console.error("Error clearing cart:", error));
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error updating stock:", error));
        })
        .catch(error => console.error("Error fetching cart:", error));
});

</script>
</body>
</html>
