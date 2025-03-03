const IMS_URL = "http://192.168.100.30:5000";
const POS_URL = "http://192.168.100.30:5001";

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
  fetch(`${IMS_URL}/api/products`)
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
                          <p class="card-text card-text d-inline-block text-truncate" style="cursor: pointer;" data-bs-toggle="tooltip" title="${product.description});">${product.description}</p>
                          <p class="card-text">₱${product.base_price}</p>
                          <p class="card-text stock" data-id="${product.pid}">Stock: ${product.quantity}</p>
                          <button class="btn add-to-cart"
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
        .then((response) => response.text()) // Change to .text() to see raw output
        .then((data) => {
          console.log("🛒 Raw Server Response:", data); // Check what the server is returning

          try {
            const jsonData = JSON.parse(data); // Attempt to parse as JSON
            if (jsonData.status === "error") {
              alert(jsonData.message);
            } else {
              alert("✅ Added to cart successfully!");
              loadCart(); // Refresh the cart
            }
          } catch (e) {
            console.error("❌ JSON Parse Error:", e, "Raw Response:", data);
          }
        })
        .catch((error) => console.error("❌ Fetch Error:", error));
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

        fetch(`${IMS_URL}/api/update_stock`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ cart }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              let orderId = data.order_ids[0]; // Get order_id from update stock

              fetch("controllers/add_sales_and_delivery.php", {
                method: "POST",
                headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `total_amount=${totalAmount}&order_id=${orderId}`,
              })
                .then((response) => response.json())
                .then((salesData) => {
                  if (salesData.status === "success") {
                    // Generate QR Code on POS
                    fetch(`${POS_URL}/generate_qr`, {
                      method: "POST",
                      headers: { "Content-Type": "application/json" },
                      body: JSON.stringify({ order_id: orderId }),
                    })
                      .then((response) => response.json())
                      .then((qrData) => {
                        if (qrData.status === "success") {
                          alert("Checkout successful! QR Code generated.");
                          window.open(qrData.qr_code, "_blank");
                        } else {
                          alert("QR Code generation failed.");
                        }
                      });

                    // Clear the cart
                    fetch("controllers/clear_cart.php", { method: "POST" })
                      .then(() => {
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

function loadPage(page) {
  fetch(page + ".php")
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;

      if (page === "sales_report") {
        console.log("✅ Sales report loaded.");
        setTimeout(() => {
          loadSalesChart();
          attachPredictionEvent();
        }, 300);
      } else if (page === "inventory_report") {
        console.log("✅ Inventory report loaded.");
        setTimeout(() => {
          loadInventoryReport();
        }, 300);
      }
    })
    .catch((error) => console.error("❌ Error loading page:", error));
}

function loadInventoryReport() {
  fetch(`${IMS_URL}/api/inventory`) // Replace with your actual API URL
    .then((response) => response.json())
    .then((data) => {
      console.log("✅ Inventory Data Received:", data);
      let inventoryTable = document.getElementById("inventoryData");
      inventoryTable.innerHTML = "";

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
    .catch((error) =>
      console.error("❌ Error fetching inventory data:", error)
    );
}

document.addEventListener("DOMContentLoaded", function () {
  loadInventoryReport();
});

function attachPredictionEvent() {
  let predictBtn = document.getElementById("predictBtn");
  let monthsInput = document.getElementById("monthsInput");
  let output = document.getElementById("predictedSalesOutput");

  if (!predictBtn) {
    console.error("❌ Predict button not found!");
    return;
  }

  predictBtn.addEventListener("click", function () {
    let inputMonths = parseInt(monthsInput.value);
    console.log("📢 Predict Button Clicked with input:", inputMonths);

    if (!isNaN(inputMonths) && inputMonths > 0) {
      fetch("./controllers/sales_data.php")
        .then((response) => response.json())
        .then((data) => {
          console.log("✅ Sales Data for Prediction:", data);

          const dates = data.map((_, i) => i + 1);
          const sales = data.map((entry) => parseFloat(entry.total_sales));

          const { m, b } = linearRegression(dates, sales);
          console.log("📊 Regression Coefficients:", {
            m,
            b,
          });

          let futureMonth = dates.length + inputMonths;
          let predictedSales = m * futureMonth + b;
          console.log(
            `📈 Predicted Sales for Month ${futureMonth}: ₱${predictedSales.toFixed(
              2
            )}`
          );

          output.innerText = `Predicted Sales: ₱${predictedSales.toFixed(2)}`;
        })
        .catch((error) =>
          console.error("❌ Error fetching sales data:", error)
        );
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
  let sumXX = x.map((xi) => xi * xi).reduce((a, b) => a + b, 0);

  let m = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
  let b = (sumY - m * sumX) / n;

  return {
    m,
    b,
  };
}

function loadSalesChart() {
  fetch("./controllers/sales_data.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Sales Data Received:", data); // Debugging

      if (!data || data.length === 0) {
        console.error("No sales data available");
        return;
      }

      setTimeout(() => {
        // Delay to ensure canvas is fully loaded
        const canvas = document.getElementById("salesChart");
        if (!canvas) {
          console.error("Error: 'salesChart' element not found!");
          return;
        }

        const ctx = canvas.getContext("2d");

        // Destroy previous chart instance if it exists
        if (window.salesChartInstance) {
          window.salesChartInstance.destroy();
        }

        const dates = data.map((entry) => entry.date);
        const sales = data.map((entry) => parseFloat(entry.total_sales)); // Ensure numbers

        console.log("Dates:", dates);
        console.log("Sales:", sales);

        window.salesChartInstance = new Chart(ctx, {
          type: "bar",
          data: {
            labels: dates,
            datasets: [
              {
                label: "Total Sales (₱)",
                data: sales,
                backgroundColor: "#3a4750",
                borderColor: "#eeeeee",
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
              },
            },
          },
        });

        console.log("Chart successfully rendered!");
      }, 300); // Small delay
    })
    .catch((error) => console.error("Error fetching sales data:", error));
}

// Function to reattach search functionality
function attachSearchFunctionality() {
  let searchBox = document.getElementById("searchBox");
  if (!searchBox) return; // Prevent errors if search box is not present

  searchBox.addEventListener("keyup", function () {
    let searchQuery = this.value.trim();

    let xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "./controllers/search_item.php?search=" + encodeURIComponent(searchQuery),
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
}

// Reattach search function when the page loads
document.addEventListener("DOMContentLoaded", function () {
  attachSearchFunctionality();
});

document
  .getElementById("uploadQRButton")
  .addEventListener("click", function () {
    let fileInput = document.getElementById("qrCodeInput");

    if (fileInput.files.length === 0) {
      alert("Please select a QR code file to upload.");
      return;
    }

    let formData = new FormData();
    formData.append("qr_code", fileInput.files[0]);

    fetch(`${POS_URL}/confirm_delivery`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          document.getElementById("deliveryStatus").innerText = data.message;
          alert("Delivery confirmed!");
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error uploading QR code:", error);
        alert("An error occurred. Please try again.");
      });
  });
