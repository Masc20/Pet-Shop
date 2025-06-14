<?php require_once 'views/layout/header.php'; ?>

<?php
$status = $_GET['status'] ?? '';
$query = $_GET['query'] ?? '';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/admin_sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manage Pet Orders</h2>
            </div>

            <!-- Search and Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="query" class="form-control" placeholder="Search by order number..." value="<?php echo htmlspecialchars($query ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No pet orders found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Pet</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                        <td data-label="Order Number"><?php echo $order['order_number']; ?></td>
                                        <td data-label="Pet">
                                                <div class="d-flex align-items-center">
                                                <img src="<?php echo BASE_URL . htmlspecialchars($order['pet_image']); ?>" 
                                                     alt="<?php echo $order['pet_name']; ?>" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold"><?php echo $order['pet_name']; ?></div>
                                                    <small class="text-muted"><?php echo $order['breed']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                        <td data-label="Customer">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold"><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></span>
                                                    <small class="text-muted"><?php echo $order['email']; ?></small>
                                                </div>
                                            </td>
                                        <td data-label="Amount">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td data-label="Status">
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'cancelled' => 'secondary'
                                                ][$order['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                        <td data-label="Payment"><?php echo ucfirst($order['payment_method']); ?></td>
                                        <td data-label="Created Date"><?php echo date('M d, Y H:i A', strtotime($order['created_at'])); ?></td>
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
                                                               data-status="approved">
                                                                <i class="fas fa-check text-success"></i> Approve
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                               data-order-id="<?php echo $order['id']; ?>"
                                                               data-status="rejected">
                                                                <i class="fas fa-times text-danger"></i> Reject
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

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $currentPage == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>&query=<?php echo urlencode($query ?? ''); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
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
            <form action="<?php echo BASE_URL; ?>/admin/pet-orders/update-status" method="POST">
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
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });

    // Handle status update modal
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
    
    /* Adjust pet image and info layout */
    .d-flex.align-items-center {
        justify-content: flex-end;
    }
    
    .d-flex.align-items-center img {
        margin-left: 0.5rem;
        margin-right: 0;
    }
    
    /* Adjust customer info layout */
    .d-flex.flex-column {
        align-items: flex-end;
    }
    
    .d-flex.flex-column small {
        margin-top: 0.25rem;
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