<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #00adb5;">
            <h4 class="mb-0 text-center">Sales Report</h4>
        </div>
        <div class="card-body">
            <div class="text-center">
                <canvas id="salesChart" style="max-width: 1200px;"></canvas>
            </div>
            <div class="text-center mt-4">
                <input type="number" id="monthsInput" class="form-control w-25 d-inline text-black" placeholder="Enter months">
                <button class="btn btn-primary" id="predictBtn">Predict Sales</button>
                <h5 class="mt-3" id="predictedSalesOutput"></h5>
            </div>
        </div>
    </div>
</div>
