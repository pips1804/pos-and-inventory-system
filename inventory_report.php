<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #00adb5;">
            <h4 class="mb-0 text-center">Inventory Report</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Starting Inventory</th>
                        <th>Inventory Received</th>
                        <th>Inventory Shipped</th>
                        <th>Inventory on Hand</th>
                    </tr>
                </thead>
                <tbody id="inventoryData">
                    <!-- Data will be inserted dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
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
</script>
