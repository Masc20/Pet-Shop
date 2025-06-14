<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/admin_sidebar.php'; ?>
        </div>

        <div class="col-md-10">
            <div class="py-4">
                <h2>Manage Orders</h2>
            </div>
            
            <!-- Search and Filter Form for Admin Orders -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/orders">
                        <div class="row g-3">
                            <div class="col-md">
                                <input type="text" name="q" class="form-control" placeholder="Search by Order Number, Customer Name, or Email..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                            </div>
                            <div class="col-md">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] === 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo (isset($_GET['status']) && $_GET['status'] === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo (isset($_GET['status']) && $_GET['status'] === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md">
                                <input type="date" name="start_date" class="form-control" title="Order Start Date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
                            </div>
                             <div class="col-md">
                                <input type="date" name="end_date" class="form-control" title="Order End Date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                <?php if (isset($_GET['q']) || isset($_GET['status']) || isset($_GET['start_date']) || isset($_GET['end_date'])): ?>
                                     <a href="<?php echo BASE_URL; ?>/admin/orders" class="btn btn-outline-secondary">Clear Filters</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- <div class="table-responsive"> -->
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-striped" style="z-index: 11110;">
                            <thead>
                                <tr>
                                        <th>Order Number</th>
                                        <th>
                                            Customer
                                            <div class="btn-group btn-group-sm ms-2">
                                                <a href="?sort=first_name&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by customer name (A to Z)">
                                                    <i class="fas fa-sort-alpha-down"></i>
                                                </a>
                                                <a href="?sort=first_name&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by customer name (Z to A)">
                                                    <i class="fas fa-sort-alpha-up"></i>
                                                </a>
                                            </div>
                                        </th>
                                        <th>
                                            Total Amount
                                            <div class="btn-group btn-group-sm ms-2">
                                                <a href="?sort=total_amount&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by amount (lowest to highest)">
                                                    <i class="fas fa-sort-numeric-down"></i>
                                                </a>
                                                <a href="?sort=total_amount&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by amount (highest to lowest)">
                                                    <i class="fas fa-sort-numeric-up"></i>
                                                </a>
                                            </div>
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                    <th>Shipped Date</th>
                                    <th>Delivered Date</th>
                                    <th>Cancelled Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                        <td data-label="Order Number"><?php echo $order['order_number']; ?></td>
                                        <td data-label="Customer">
                                        <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                    </td>
                                        <td data-label="Total Amount">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td data-label="Status">
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
                                        <td data-label="Shipped Date">
                                        <?php echo $order['shipped_date'] ? date('M d, Y h:i A', strtotime($order['shipped_date'])) : '-'; ?>
                                    </td>
                                        <td data-label="Delivered Date">
                                        <?php echo $order['delivery_date'] ? date('M d, Y h:i A', strtotime($order['delivery_date'])) : '-'; ?>
                                    </td>
                                        <td data-label="Cancelled Date">
                                        <?php echo $order['cancelled_date'] ? date('M d, Y h:i A', strtotime($order['cancelled_date'])) : '-'; ?>
                                    </td>
                                        <td data-label="Actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal" 
                                                       data-order-id="<?php echo $order['id']; ?>"
                                                       data-status="pending">
                                                        <i class="fas fa-clock text-warning"></i> Pending
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                       data-order-id="<?php echo $order['id']; ?>"
                                                       data-status="processing">
                                                        <i class="fas fa-cog text-info"></i> Processing
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                       data-order-id="<?php echo $order['id']; ?>"
                                                       data-status="shipped">
                                                        <i class="fas fa-shipping-fast text-primary"></i> Shipped
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                       data-order-id="<?php echo $order['id']; ?>"
                                                       data-status="delivered">
                                                        <i class="fas fa-check text-success"></i> Delivered
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                       data-order-id="<?php echo $order['id']; ?>"
                                                       data-status="cancelled">
                                                        <i class="fas fa-ban text-danger"></i> Cancel Order
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav aria-label="Admin order pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/admin/orders?page=<?php echo $currentPage - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>"><a class="page-link" href="<?php echo BASE_URL; ?>/admin/orders?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/admin/orders?page=<?php echo $currentPage + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

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
            <form id="updateStatusForm" action="<?php echo BASE_URL; ?>/admin/orders/update-status" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="updateOrderId">
                    <input type="hidden" name="status" id="updateStatus">
                    
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateStatusModal = document.getElementById('updateStatusModal');
    
    if (updateStatusModal) {
        updateStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            const status = button.getAttribute('data-status');
            
            document.getElementById('updateOrderId').value = orderId;
            document.getElementById('updateStatus').value = status;
            document.querySelector('textarea[name="admin_notes"]').value = '';
        });
    }

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<style>
/* Responsive table styles */
@media screen and (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    
    .table thead {
        display: none;
    }
    
    .table tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .table td {
        display: block;
        text-align: right;
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
        position: relative;
    }
    
    .table td:last-child {
        border-bottom: 0;
    }
    
    .table td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #6c757d;
        margin-right: 1rem;
    }
    
    /* Adjust dropdown for mobile */
    .dropdown {
        width: 100%;
    }
    
    .dropdown .btn {
        width: 100%;
        text-align: left;
    }
    
    .dropdown-menu {
        width: 100%;
    }
    
    /* Adjust badge styles for mobile */
    .badge {
        display: inline-block;
        padding: 0.5em 0.75em;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    /* Improve spacing for empty date fields */
    .table td:empty::after {
        content: '-';
        color: #6c757d;
    }
}

/* Existing pagination styles */
.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: #FF8C00;
}

.pagination .page-link:hover {
    background: #FF8C00;
    color: white;
}

.pagination .page-item.active .page-link {
    background: #FF8C00;
    border-color: #FF8C00;
}
</style>

<?php require_once 'views/layout/footer.php'; ?>