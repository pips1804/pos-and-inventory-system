function loadPage(page) {
  if (page === "delivery_report") {
    fetch("./controllers/fetch_deliveries.php")
      .then((response) => response.json())
      .then((data) => {
        console.log("✅ Delivery Data Received:", data);

        if (!data || data.length === 0) {
          document.getElementById(
            "content"
          ).innerHTML = `<p class="text-center text-muted">No delivery records found.</p>`;
          return;
        }

        let tableHTML = `
  <div class="container mt-4">
      <div class="card shadow-sm">
          <div class="card-header text-white text-center" style="background-color: #00adb5;">
              <h4 class="mb-0">Delivery Report</h4>
          </div>
          <div class="card-body">
              <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                  <table class="table table-dark">
                      <thead class="table-dark">
                          <tr>
                              <th>#</th>
                              <th>Order ID</th>
                              <th>Total (₱)</th>
                              <th>Customer ID</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                      <tbody id="deliveryTableBody">
  `;

        let index = 1;
        data.forEach((delivery) => {
          tableHTML += `
        <tr>
          <td class="text">${index}</td>
          <td class="text">${delivery.order_id}</td>
          <td class="text">₱${delivery.total}</td>
          <td class="text">${delivery.customer_id}</td>
          <td class="text">${
            delivery.delivered == 1 ? "Delivered ✅" : "Pending ❌"
          }</td>
        </tr>
    `;
          index++;
        });

        tableHTML += `
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>

  <div class="container text-center mt-4">
  <div class="card p-4 shadow-lg border-0 text-center">
      <h4 class="mb-3">Confirm Delivery</h4>
      <input type="file" id="qrCodeInput" class="form-control mb-3 text-dark" accept="image/*">
      <img id="imagePreview" class="img-fluid mb-3" style="max-width: 200px; display: none;" />
      <button id="uploadQRButton" class="btn btn-primary w-100">Confirm Delivery</button>
      <p id="deliveryStatus" class="mt-3"></p>
  </div>
  </div>
  `;

        document.getElementById("content").innerHTML = tableHTML;
        console.log("✅ Delivery Report Loaded.");

        setTimeout(() => {
          let qrInput = document.getElementById("qrCodeInput");
          let uploadBtn = document.getElementById("uploadQRButton");

          if (qrInput) {
            qrInput.addEventListener("change", function (event) {
              const file = event.target.files[0];
              if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                  const imgPreview = document.getElementById("imagePreview");
                  imgPreview.src = e.target.result;
                  imgPreview.style.display = "block";
                };
                reader.readAsDataURL(file);
              }
            });
          } else {
            console.error("❌ qrCodeInput not found!");
          }

          if (uploadBtn) {
            uploadBtn.addEventListener("click", function () {
              let fileInput = document.getElementById("qrCodeInput");

              if (fileInput.files.length === 0) {
                alert("Please select a QR code file to upload.");
                return;
              }

              let formData = new FormData();
              formData.append("qr_code", fileInput.files[0]);

              fetch("http://192.168.100.30:5001/confirm_delivery", {
                method: "POST",
                body: formData,
              })
                .then((response) => response.json())
                .then((data) => {
                  console.log("Server Response:", data);

                  if (data.status === "success") {
                    document.getElementById("deliveryStatus").innerText =
                      data.message;
                    alert("Delivery confirmed!");

                    // Ensure order ID is treated as a string for reliable comparison
                    let orderId = String(data.order_id).trim();
                    let tableRows = document.querySelectorAll(
                      "#deliveryTableBody tr"
                    );

                    tableRows.forEach((row) => {
                      let orderCell = row.cells[1]; // Order ID column
                      let orderCellText = orderCell.innerText.trim();

                      console.log(`Comparing: ${orderCellText} vs ${orderId}`); // Debugging

                      if (orderCellText === orderId) {
                        let statusCell = row.cells[4]; // Status column
                        statusCell.innerHTML = "Delivered ✅";
                        console.log(`Updated status for Order ID ${orderId}`);
                      }
                    });
                  } else {
                    alert("Error: " + data.message);
                  }
                })
                .catch((error) => {
                  console.error("Fetch Error:", error);
                  alert("An error occurred. Check console for details.");
                });
            });
          } else {
            console.error("❌ uploadQRButton not found!");
          }
        }, 100);
      })
      .catch((error) =>
        console.error("❌ Error fetching delivery data:", error)
      );
  } else {
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
}

function loadInventoryReport() {
  fetch("http://192.168.100.30:5000/api/inventory") // Replace with your actual API URL
    .then((response) => response.json())
    .then((data) => {
      console.log("✅ Inventory Data Received:", data);
      let inventoryTable = document.getElementById("inventoryData");
      inventoryTable.innerHTML = "";

      data.forEach((item, index) => {
        inventoryTable.innerHTML += `
                <tr>
                    <td class="text">${index + 1}</td>
                    <td class="text">${item.product_name}</td>
                    <td class="text">${item.starting_inventory}</td>
                    <td class="text">${item.inventory_received}</td>
                    <td class="text">${item.inventory_shipped}</td>
                    <td class="text">${item.inventory_on_hand}</td>
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
      console.log("✅ Sales Data Received:", data); // Debugging

      if (!data || data.length === 0) {
        console.error("❌ No sales data available");
        return;
      }

      setTimeout(() => {
        const canvas = document.getElementById("salesChart");
        if (!canvas) {
          console.error("❌ Error: 'salesChart' element not found!");
          return;
        }

        const ctx = canvas.getContext("2d");

        if (window.salesChartInstance) {
          window.salesChartInstance.destroy();
        }

        const months = data.map((entry) => entry.month); // Labels: '2025-01', '2025-02'
        const sales = data.map((entry) => parseFloat(entry.total_sales));

        console.log("📊 Months:", months);
        console.log("📈 Sales:", sales);

        window.salesChartInstance = new Chart(ctx, {
          type: "bar",
          data: {
            labels: months,
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
              y: { beginAtZero: true },
            },
          },
        });

        console.log("✅ Chart successfully rendered!");
      }, 300);
    })
    .catch((error) => console.error("❌ Error fetching sales data:", error));
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
