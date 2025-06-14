<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/user_sidebar.php'; ?>
        </div>
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h2>My Product Orders</h2>
            </div>

            <!-- Search and Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search orders..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo ($filterStatus ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo ($filterStatus ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo ($filterStatus ?? '') === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo ($filterStatus ?? '') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo ($filterStatus ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="<?php echo BASE_URL;?>/user/orders" class="btn btn-secondary">Reset</a>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                            <p class="text-muted">Start shopping to see your orders here!</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <div class="d-flex align-items-center sort-header">
                                                Order Number
                                                <div class="ms-2">
                                                    <a href="?sort=order_number&order=asc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'order_number' && isset($_GET['order']) && $_GET['order'] === 'asc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by order number (A-Z)">
                                                        <i class="fas fa-sort-alpha-up"></i>
                                                    </a>
                                                    <a href="?sort=order_number&order=desc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'order_number' && isset($_GET['order']) && $_GET['order'] === 'desc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by order number (Z-A)">
                                                        <i class="fas fa-sort-alpha-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-center sort-header">
                                                Date
                                                <div class="ms-2">
                                                    <a href="?sort=order_date&order=asc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'order_date' && isset($_GET['order']) && $_GET['order'] === 'asc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by date (oldest first)">
                                                        <i class="fas fa-sort-numeric-up"></i>
                                                    </a>
                                                    <a href="?sort=order_date&order=desc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'order_date' && isset($_GET['order']) && $_GET['order'] === 'desc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by date (newest first)">
                                                        <i class="fas fa-sort-numeric-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-center sort-header">
                                                Items
                                                <div class="ms-2">
                                                    <a href="?sort=total_quantity&order=asc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'total_quantity' && isset($_GET['order']) && $_GET['order'] === 'asc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by total quantity (lowest to highest)">
                                                        <i class="fas fa-sort-numeric-up"></i>
                                                    </a>
                                                    <a href="?sort=total_quantity&order=desc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'total_quantity' && isset($_GET['order']) && $_GET['order'] === 'desc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by total quantity (highest to lowest)">
                                                        <i class="fas fa-sort-numeric-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-center sort-header">
                                                Total Amount
                                                <div class="ms-2">
                                                    <a href="?sort=total_amount&order=asc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'total_amount' && isset($_GET['order']) && $_GET['order'] === 'asc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by total (lowest to highest)">
                                                        <i class="fas fa-sort-numeric-up"></i>
                                                    </a>
                                                    <a href="?sort=total_amount&order=desc<?php echo isset($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; echo isset($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline-secondary btn-sm <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'total_amount' && isset($_GET['order']) && $_GET['order'] === 'desc') ? 'sort-active' : ''; ?>" data-bs-toggle="tooltip" title="Sort by total (highest to lowest)">
                                                        <i class="fas fa-sort-numeric-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo date('M d, Y H:i A', strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <?php 
                                                $itemCount = count($order['items']);
                                                $totalQuantity = array_sum(array_column($order['items'], 'quantity'));
                                                echo $itemCount . ' product' . ($itemCount > 1 ? 's' : '') . ' (' . $totalQuantity . ' item' . ($totalQuantity > 1 ? 's' : '') . ')';
                                                ?>
                                                <br>
                                                <small class="text-muted">
                                                    Ordered by: <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                                </small>
                                            </td>
                                            <td class="fw-bold">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($order['status']) {
                                                        'processing' => 'warning',
                                                        'shipped' => 'info',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewOrderModal<?php echo $order['id']; ?>">
                                                    <i class="fas fa-eye me-1"></i> View Details
                                                </button>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#cancelOrderModal"
                                                            data-order-id="<?php echo $order['id']; ?>"
                                                            data-bs-toggle="tooltip"
                                                            title="Cancel Order">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateStatusModal"
                                                            data-order-id="<?php echo $order['id']; ?>"
                                                            data-order-number="<?php echo $order['order_number']; ?>">
                                                        <i class="fas fa-check"></i> Process
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($order['status'] === 'processing'): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#shipOrderModal"
                                                            data-order-id="<?php echo $order['id']; ?>"
                                                            data-order-number="<?php echo $order['order_number']; ?>">
                                                        <i class="fas fa-shipping-fast"></i> Ship
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($order['status'] === 'shipped'): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deliverOrderModal"
                                                            data-order-id="<?php echo $order['id']; ?>"
                                                            data-order-number="<?php echo $order['order_number']; ?>">
                                                        <i class="fas fa-check-circle"></i> Mark as Delivered
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($searchQuery ?? ''); ?>&status=<?php echo urlencode($filterStatus ?? ''); ?>&sort=<?php echo urlencode($sortBy ?? ''); ?>&order=<?php echo urlencode($sortOrder ?? ''); ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery ?? ''); ?>&status=<?php echo urlencode($filterStatus ?? ''); ?>&sort=<?php echo urlencode($sortBy ?? ''); ?>&order=<?php echo urlencode($sortOrder ?? ''); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($currentPage < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($searchQuery ?? ''); ?>&status=<?php echo urlencode($filterStatus ?? ''); ?>&sort=<?php echo urlencode($sortBy ?? ''); ?>&order=<?php echo urlencode($sortOrder ?? ''); ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/user/orders/cancel" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <p>Are you sure you want to cancel this order?</p>
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for cancellation</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to process order <span id="updateOrderNumber" class="fw-bold"></span>?</p>
                <p class="text-muted">This will mark the order as processing and notify the customer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="<?php echo BASE_URL; ?>/orders/update-status" method="POST">
                    <input type="hidden" name="order_id" id="updateOrderId">
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="btn btn-success">Process Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ship Order Modal -->
<div class="modal fade" id="shipOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ship Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark order <span id="shipOrderNumber" class="fw-bold"></span> as shipped?</p>
                <p class="text-muted">This will notify the customer that their order is on the way.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="<?php echo BASE_URL; ?>/orders/update-status" method="POST">
                    <input type="hidden" name="order_id" id="shipOrderId">
                    <input type="hidden" name="status" value="shipped">
                    <button type="submit" class="btn btn-info">Ship Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Deliver Order Modal -->
<div class="modal fade" id="deliverOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Delivered</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark order <span id="deliverOrderNumber" class="fw-bold"></span> as delivered?</p>
                <p class="text-muted">This will complete the order and notify the customer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="<?php echo BASE_URL; ?>/orders/update-status" method="POST">
                    <input type="hidden" name="order_id" id="deliverOrderId">
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit" class="btn btn-success">Mark as Delivered</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Order Modals -->
<?php foreach ($orders as $order): ?>
<div class="modal fade" id="viewOrderModal<?php echo $order['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order #<?php echo $order['id']; ?> Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p class="mb-1"><strong>Date:</strong> <?php echo date('M d, Y H:i A', strtotime($order['order_date'])); ?></p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge bg-<?php
                                echo $order['status'] === 'delivered' ? 'success' : 
                                    ($order['status'] === 'processing' ? 'warning' : 
                                    ($order['status'] === 'cancelled' ? 'danger' : 'info')); 
                            ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                        <p class="mb-1"><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Delivery Address</h6>
                        <p class="mb-1">
                            <?php 
                            $address = $order['delivery_address'];
                            echo htmlspecialchars(
                                ($address['street'] ? $address['street'] . ', ' : '') .
                                ($address['barangay'] ? $address['barangay'] . ', ' : '') .
                                ($address['city'] ? $address['city'] . ', ' : '') .
                                ($address['zipcode'] ? $address['zipcode'] : '')
                            ); 
                            ?>
                        </p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo BASE_URL . $item['product_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                 class="me-2" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['type'] ?? 'N/A'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <h4 class="mt-3"><?php echo "Overall Total: ₱" . number_format($order['total_amount'], 2);?></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<style>
/* Alert animations */
.alert {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Modal backdrop blur */
.modal {
    backdrop-filter: blur(5px);
}

.modal-content {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
    transition: background-color 0.2s ease;
}

/* Badge animations */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Button hover effects */
.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Sorting styles */
.fa-sort-up, .fa-sort-down {
    color: #FF8C00;
    font-size: 0.8em;
    transition: color 0.2s ease;
}

.fa-sort-up:hover, .fa-sort-down:hover {
    color: #e67e00;
}

.sort-active {
    color: #e67e00;
    font-weight: bold;
}

.sort-header {
    cursor: pointer;
    user-select: none;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cancel Order Modal
    const cancelOrderModal = document.getElementById('cancelOrderModal');
    if (cancelOrderModal) {
        cancelOrderModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            document.getElementById('cancelOrderId').value = orderId;
        });
    }

    // Update Status Modal
    const updateStatusModal = document.getElementById('updateStatusModal');
    if (updateStatusModal) {
        updateStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            const orderNumber = button.getAttribute('data-order-number');
            
            document.getElementById('updateOrderId').value = orderId;
            document.getElementById('updateOrderNumber').textContent = orderNumber;
        });
    }

    // Ship Order Modal
    const shipOrderModal = document.getElementById('shipOrderModal');
    if (shipOrderModal) {
        shipOrderModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            const orderNumber = button.getAttribute('data-order-number');
            
            document.getElementById('shipOrderId').value = orderId;
            document.getElementById('shipOrderNumber').textContent = orderNumber;
        });
    }

    // Deliver Order Modal
    const deliverOrderModal = document.getElementById('deliverOrderModal');
    if (deliverOrderModal) {
        deliverOrderModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            const orderNumber = button.getAttribute('data-order-number');
            
            document.getElementById('deliverOrderId').value = orderId;
            document.getElementById('deliverOrderNumber').textContent = orderNumber;
        });
    }

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?> 