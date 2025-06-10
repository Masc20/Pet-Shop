<?php require_once 'views/layout/header.php'; ?>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/admin_sidebar.php'; ?>
        </div>
        
        <div class="col-md-10">
            <h1 class="fw-bold mb-4">Admin Dashboard</h1>
            
            <!-- Stats Cards -->
            <div class="row mb-4">                
                <div class="col-md-3">
                    <div class="card text-white" style="background: var(--primary-color);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $petStats['total']; ?></h4>
                                    <p class="mb-0">Total Pets</p>
                                </div>
                                <i class="fas fa-paw fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $petStats['adopted']; ?></h4>
                                    <p class="mb-0">Adopted Pets</p>
                                </div>
                                <i class="fas fa-heart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $productStats['total']; ?></h4>
                                    <p class="mb-0">Pawducts</p>
                                </div>
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>₱<?php echo number_format($orderStats['total_revenue'], 2); ?></h4>
                                    <p class="mb-0">Total Revenue</p>
                                </div>
                                <i class="fas fa-peso-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Date Range Filter -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo BASE_URL; ?>/admin" method="GET" class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo $_GET['startDate'] ?? ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="endDate" value="<?php echo $_GET['endDate'] ?? ''; ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Apply Filter</button>
                                <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Monthly Revenue</h5>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('revenueChart', 'small')">Small</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('revenueChart', 'medium')">Medium</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('revenueChart', 'large')">Large</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pet Adoptions</h5>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('adoptionsChart', 'small')">Small</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('adoptionsChart', 'medium')">Medium</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('adoptionsChart', 'large')">Large</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="adoptionsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Top Pawducts</h5>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('topProductsChart', 'small')">Small</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('topProductsChart', 'medium')">Medium</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('topProductsChart', 'large')">Large</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Low Stock Pawducts</h5>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('lowStockChart', 'small')">Small</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('lowStockChart', 'medium')">Medium</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('lowStockChart', 'large')">Large</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="lowStockChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Product Type Distribution Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pawduct Type Distribution</h5>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('productTypeChart', 'small')">Small</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('productTypeChart', 'medium')">Medium</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('productTypeChart', 'large')">Large</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="productTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pet Type Distribution Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pet Type Distribution</h5>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('petTypeChart', 'small')">Small</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('petTypeChart', 'medium')">Medium</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="resizeChart('petTypeChart', 'large')">Large</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="petTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chart-controls {
    display: flex;
    gap: 5px;
}

.chart-controls .btn {
    padding: 2px 8px;
    font-size: 0.8rem;
}

.chart-container {
    transition: height 0.3s ease;
}

.chart-container.small {
    height: 200px !important;
}

.chart-container.medium {
    height: 300px !important;
}

.chart-container.large {
    height: 400px !important;
}
</style>

<script>
// Store original data
let originalData = {
    monthlyRevenue: <?php echo json_encode($monthlyRevenue); ?>,
    petAdoptions: <?php echo json_encode($petAdoptions); ?>,
    topProducts: <?php echo json_encode($topProducts); ?>,
    lowStockProducts: <?php echo json_encode($lowStockProducts); ?>,
    productTypeDistribution: <?php echo json_encode($productTypeDistribution); ?>,
    petTypeDistribution: <?php echo json_encode($petTypeDistribution); ?>
};

// Chart instances
let revenueChart, adoptionsChart, topProductsChart, lowStockChart, productTypeChart, petTypeChart;

// Function to update all charts
function updateCharts(data) {
    if (!data) return;
    
    // Update Revenue Chart
    if (data.monthlyRevenue && data.monthlyRevenue.length > 0) {
        revenueChart.data.labels = data.monthlyRevenue.map(item => item.month);
        revenueChart.data.datasets[0].data = data.monthlyRevenue.map(item => item.revenue);
        revenueChart.update();
    }

    // Update Adoptions Chart
    if (data.petAdoptions && data.petAdoptions.length > 0) {
        adoptionsChart.data.labels = data.petAdoptions.map(item => item.month);
        adoptionsChart.data.datasets[0].data = data.petAdoptions.map(item => item.count);
        adoptionsChart.update();
    }

    // Update Top Products Chart
    if (data.topProducts && data.topProducts.length > 0) {
        topProductsChart.data.labels = data.topProducts.map(item => item.name);
        topProductsChart.data.datasets[0].data = data.topProducts.map(item => item.total_sold);
        topProductsChart.update();
    }

    // Update Low Stock Chart
    if (data.lowStockProducts && data.lowStockProducts.length > 0) {
        lowStockChart.data.labels = data.lowStockProducts.map(item => item.name);
        lowStockChart.data.datasets[0].data = data.lowStockProducts.map(item => item.stock_quantity === 0 ? -1 : item.stock_quantity);
        lowStockChart.update();
    }

    // Update Product Type Distribution Chart
    if (data.productTypeDistribution && data.productTypeDistribution.length > 0) {
        productTypeChart.data.labels = data.productTypeDistribution.map(item => item.type.charAt(0).toUpperCase() + item.type.slice(1));
        productTypeChart.data.datasets[0].data = data.productTypeDistribution.map(item => item.count);
        productTypeChart.update();
    }

    // Update Pet Type Distribution Chart
    if (data.petTypeDistribution && data.petTypeDistribution.length > 0) {
        petTypeChart.data.labels = data.petTypeDistribution.map(item => item.type.charAt(0).toUpperCase() + item.type.slice(1));
        petTypeChart.data.datasets[0].data = data.petTypeDistribution.map(item => item.count);
        petTypeChart.update();
    }
}

// Initialize charts with the data
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: originalData.monthlyRevenue.map(item => {
                const [year, month] = item.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Revenue',
                data: originalData.monthlyRevenue.map(item => parseFloat(item.revenue)),
                borderColor: '#FF8C00',
                backgroundColor: 'transparent',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₱' + parseFloat(context.raw).toLocaleString();
                        }
                    }
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
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Adoptions Chart
    const adoptionsCtx = document.getElementById('adoptionsChart').getContext('2d');
    adoptionsChart = new Chart(adoptionsCtx, {
        type: 'bar',
        data: {
            labels: originalData.petAdoptions.map(item => item.month),
            datasets: [{
                label: 'Approved Adoptions',
                data: originalData.petAdoptions.map(item => item.count),
                backgroundColor: '#28a745',
                borderColor: '#28a745',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    topProductsChart = new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: originalData.topProducts.map(item => item.name),
            datasets: [{
                label: 'Units Sold',
                data: originalData.topProducts.map(item => item.total_sold),
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            indexAxis: 'y'
        }
    });

    // Low Stock Chart
    const lowStockCtx = document.getElementById('lowStockChart').getContext('2d');
    lowStockChart = new Chart(lowStockCtx, {
        type: 'bar',
        data: {
            labels: originalData.lowStockProducts.map(item => item.name),
            datasets: [{
                label: 'Stock Level',
                data: originalData.lowStockProducts.map(item => item.stock_quantity === 0 ? -1 : item.stock_quantity),
                backgroundColor: function(context) {
                    const value = context.dataset.data[context.dataIndex];
                    return value === -1 ? '#dc3545' : // Yellow for out of stock
                           value <= 5 ? '#ffc107' : // Red for low stock
                           '#28a745'; // Green for normal stock
                },
                borderColor: function(context) {
                    const value = context.dataset.data[context.dataIndex];
                    return value === -1 ? '#dc3545' :
                           value <= 5 ? '#ffc107' :
                           '#28a745';
                },
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        generateLabels: function(chart) {
                            return [
                                {
                                    text: 'Out of Stock',
                                    fillStyle: '#ffc107',
                                    strokeStyle: '#ffc107',
                                    lineWidth: 1
                                },
                                {
                                    text: 'Low Stock (1-5)',
                                    fillStyle: '#dc3545',
                                    strokeStyle: '#dc3545',
                                    lineWidth: 1
                                },
                            ];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            let status = '';
                            if (value === -1) {
                                status = ' (Out of Stock)';
                            } else if (value <= 5) {
                                status = ' (Low Stock)';
                            }
                            return `Stock Level: ${value === -1 ? 0 : value}${status}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    suggestedMin: -1,
                    ticks: {
                        stepSize: 1,
                        callback: function(value){
                            return value === -1 ? '' : value;
                        }
                    }
                }
            }
        }
    });

    // Product Type Distribution Chart
    const productTypeCtx = document.getElementById('productTypeChart').getContext('2d');
    productTypeChart = new Chart(productTypeCtx, {
        type: 'pie',
        data: {
            labels: originalData.productTypeDistribution.map(item => item.type.charAt(0).toUpperCase() + item.type.slice(1)),
            datasets: [{
                data: originalData.productTypeDistribution.map(item => item.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Pet Type Distribution Chart
    const petTypeCtx = document.getElementById('petTypeChart').getContext('2d');
    petTypeChart = new Chart(petTypeCtx, {
        type: 'pie',
        data: {
            labels: originalData.petTypeDistribution.map(item => item.type.charAt(0).toUpperCase() + item.type.slice(1)),
            datasets: [{
                data: originalData.petTypeDistribution.map(item => item.count),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});

// Chart resize function
function resizeChart(chartId, size) {
    const container = document.querySelector(`#${chartId}`).parentElement;
    container.className = 'chart-container ' + size;
    
    // Update the chart
    const chart = Chart.getChart(chartId);
    if (chart) {
        chart.resize();
    }
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
