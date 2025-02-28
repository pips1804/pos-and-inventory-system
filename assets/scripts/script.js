function showLogoutModal() {
  var logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
  logoutModal.show();
}

function showRemoveModal() {
  var removeModal = new bootstrap.Modal(document.getElementById("removeModal"));
  removeModal.show();
}

function showRemoveAllModal() {
  var removeAllModal = new bootstrap.Modal(
    document.getElementById("removeAllModal")
  );
  removeAllModal.show();
}

function disableCheckoutButton() {
  document.getElementById("checkoutButton").disabled = true;
  document.getElementById("checkoutButton").innerText = "Processing...";
}

document.getElementById("searchBox").addEventListener("keyup", function () {
  let searchQuery = this.value.trim();

  let xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "./././controllers/search_item.php?search=" +
      encodeURIComponent(searchQuery),
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.querySelector(".products-container").innerHTML =
        xhr.responseText;
    }
  };

  xhr.send();
});

document.addEventListener("DOMContentLoaded", function () {
  fetchProducts();
  loadCart();
});

function fetchProducts() {
  fetch("http://192.168.100.8:5000/api/products")
    .then((response) => response.json())
    .then((data) => {
      const productsContainer = document.querySelector(".products-container");
      productsContainer.innerHTML = ""; // Clear existing products

      data.forEach((product) => {
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
    .catch((error) => console.error("Error fetching products:", error));
}

setInterval(fetchProducts, 1000);

function attachAddToCartEvent() {
  document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", function () {
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
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `product_id=${productId}&quantity=${quantity}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "error") {
            alert(data.message);
          } else {
            alert("Added to cart successfully!");
            loadCart(); // Refresh the cart
          }
        })
        .catch((error) => console.error("Error adding to cart:", error));
    });
  });
}

function loadCart() {
  fetch("./controllers/get_cart.php")
    .then((response) => response.json())
    .then((cart) => {
      const cartItems = document.getElementById("cart-items");
      cartItems.innerHTML = "";

      let totalAmount = 0;

      cart.forEach((item) => {
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
    .catch((error) => console.error("Error loading cart:", error));
}

function attachRemoveCartEvent() {
  document.querySelectorAll(".remove-from-cart").forEach((button) => {
    button.addEventListener("click", function () {
      const productId = this.getAttribute("data-id");

      fetch("./controllers/remove_from_cart.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `product_id=${productId}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            loadCart();
          } else {
            alert(data.message);
          }
        })
        .catch((error) => console.error("Error removing from cart:", error));
    });
  });
}

document
  .getElementById("checkoutButton")
  .addEventListener("click", function () {
    fetch("controllers/get_cart.php")
      .then((response) => response.json())
      .then((cart) => {
        let totalAmount = cart.reduce(
          (sum, item) => sum + item.quantity * item.price,
          0
        );

        // Update stock first
        fetch("http://192.168.100.8:5000/api/update_stock", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            cart,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              // Insert into sales and deliver table
              fetch("controllers/add_sales_and_delivery.php", {
                method: "POST",
                headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `total_amount=${totalAmount}`,
              })
                .then((response) => response.json())
                .then((salesData) => {
                  if (salesData.status === "success") {
                    // Clear the cart after checkout
                    fetch("controllers/clear_cart.php", {
                      method: "POST",
                    })
                      .then(() => {
                        alert("Checkout successful!");
                        loadCart();
                      })
                      .catch((error) =>
                        console.error("Error clearing cart:", error)
                      );
                  } else {
                    alert("Error: " + salesData.message);
                  }
                })
                .catch((error) =>
                  console.error("Error inserting sales/delivery:", error)
                );
            } else {
              alert("Stock update failed: " + data.message);
            }
          })
          .catch((error) => console.error("Error updating stock:", error));
      })
      .catch((error) => console.error("Error fetching cart:", error));
  });
