<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/user_sidebar.php'; ?>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3 border-bottom">
                <h2>Dashboard</h2>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Total Products</h6>
                                    <h2 class="mt-2 mb-0"><?php echo $totalProducts; ?></h2>
                                </div>
                                <i class="fas fa-box fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Total Orders</h6>
                                    <h2 class="mt-2 mb-0"><?php echo $totalOrders; ?></h2>
                                </div>
                                <i class="fas fa-shopping-bag fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Low Stock Items</h6>
                                    <h2 class="mt-2 mb-0"><?php echo $lowStockCount; ?></h2>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Total Revenue</h6>
                                    <h2 class="mt-2 mb-0">₱<?php echo number_format($totalRevenue, 2); ?></h2>
                                </div>
                                <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <!-- Product Type Distribution -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Product Type Distribution</h5>
                            <canvas id="productTypeChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Monthly Sales -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Monthly Sales</h5>
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders and Low Stock Products -->
            <div class="row">
                <!-- Recent Orders -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Recent Orders</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $order['status'] === 'delivered' ? 'success' : 
                                                            ($order['status'] === 'processing' ? 'warning' : 
                                                            ($order['status'] === 'cancelled' ? 'danger' : 'info')); 
                                                    ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Low Stock Products -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock Products</h5>
                            <div class="list-group">
                                <?php foreach ($lowStockProducts as $product): ?>
                                    <a href="<?php echo BASE_URL;?>/user/products" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-danger"><?php echo $product['stock_quantity']; ?> left</small>
                                        </div>
                                        <small class="text-muted"><?php echo ucfirst($product['type']); ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Product Type Distribution Chart
const productTypeCtx = document.getElementById('productTypeChart').getContext('2d');
new Chart(productTypeCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($productTypeData, 'type')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($productTypeData, 'count')); ?>,
            backgroundColor: ['#28a745', '#17a2b8'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});

// Monthly Sales Chart
const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
new Chart(monthlySalesCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($monthlySales, 'month')); ?>,
        datasets: [{
            label: 'Sales',
            data: <?php echo json_encode(array_column($monthlySales, 'total')); ?>,
            borderColor: '#0d6efd',
            tension: 0.1,
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?> 