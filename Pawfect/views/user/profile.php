<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/user_sidebar.php'; ?>
        </div>
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h2>My Profile</h2>
            </div>

            <!-- Profile Information -->
            <div class="row">
            <div class="col-md-4">
                    <div class="card mb-4">
                    <div class="card-body text-center">
                            <?php if ($user && $user['avatar']):?>
                                <img src="<?php echo BASE_URL.$user['avatar']; ?>" 
                                    alt="Profile Picture" 
                                    class="rounded-circle img-fluid mb-3" 
                                    style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #FF8C00;">
                        <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                            <h5 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </button>
                    </div>
                </div>
            </div>

                    <!-- Delivery Address -->
                    <div class="card mb-4">
                    <div class="card-header">
                            <h5 class="card-title mb-0">Delivery Address</h5>
                    </div>
                    <div class="card-body">
                            <?php if (!empty($delivery_address)): ?>
                                <p class="mb-1">
                                    <strong>Street:</strong> <?php echo htmlspecialchars($delivery_address['street']); ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Barangay:</strong> <?php echo htmlspecialchars($delivery_address['barangay']); ?>
                                </p>
                                <p class="mb-1">
                                    <strong>City:</strong> <?php echo htmlspecialchars($delivery_address['city']); ?>
                                </p>
                                <p class="mb-0">
                                    <strong>Zip Code:</strong> <?php echo htmlspecialchars($delivery_address['zipcode']); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted mb-0">No delivery address set</p>
                            <?php endif; ?>
                            <button type="button" class="btn btn-outline-warning btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#updateAddressModal">
                                <i class="fas fa-map-marker-alt"></i> Update Address
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Active Orders -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Active Orders</h5>
                    </div>
                    <div class="card-body">
                            <?php if (!empty($orders)): ?>
                            <div class="table-responsive">
                                    <table class="table table-hover">
                                    <thead>
                                        <tr>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
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
                                                <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#viewOrderModal<?php echo $order['id']; ?>">
                                                            <i class="fas fa-eye"></i> View
                                                        </button>
                                                            <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal<?php echo $order['id']; ?>">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                                <?php if ($totalPages > 1): ?>
                                    <nav aria-label="Page navigation" class="mt-3">
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">No active orders found</p>
                        <?php endif; ?>
                    </div>
                </div>

                    <!-- Adopted Pets -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">My Adopted Pets</h5>
                    </div>
                    <div class="card-body">
                            <?php if (!empty($adoptedPets)): ?>
                                <div class="row">
                                    <?php foreach ($adoptedPets as $pet): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <img src="<?php echo BASE_URL.$pet['pet_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($pet['name']); ?>" style="height: 200px; object-fit: cover;">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($pet['name']); ?></h5>
                                                    <p class="card-text">
                                                        <small class="text-muted">Adopted on: <?php echo date('M d, Y', strtotime($pet['adoption_date'])); ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No adopted pets found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
                    </div>
                </div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                                        </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Save Changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Address Modal -->
<div class="modal fade" id="updateAddressModal" tabindex="-1">
    <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Update Delivery Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            <form method="POST" action="<?php echo BASE_URL;?>/profile/update-address">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($delivery_address['city'] ?? ''); ?>" required>
                        </div>
                    <div class="mb-3">
                        <label class="form-label">Barangay</label>
                        <input type="text" class="form-control" name="barangay" value="<?php echo htmlspecialchars($delivery_address['barangay'] ?? ''); ?>" required>
                        </div>
                    <div class="mb-3">
                        <label class="form-label">Street</label>
                        <input type="text" class="form-control" name="street" value="<?php echo htmlspecialchars($delivery_address['street'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Zip Code</label>
                        <input type="text" class="form-control" name="zipcode" value="<?php echo htmlspecialchars($delivery_address['zipcode'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Save Address</button>
                </div>
            </form>
            </div>
        </div>
    </div>

<!-- Cancel Order Confirmation Modals -->
<?php foreach ($orders as $order): ?>
<?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
        <div class="modal fade" id="cancelOrderModal<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                <p>Are you sure you want to cancel order #<?php echo $order['id']; ?>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                <form action="<?php echo BASE_URL; ?>/orders/cancel" method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn btn-danger">Yes, Cancel Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

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
                        <p class="mb-1"><?php echo htmlspecialchars($delivery_address['street']); ?></p>
                        <p class="mb-1"><?php echo htmlspecialchars($delivery_address['barangay']); ?></p>
                        <p class="mb-1"><?php echo htmlspecialchars($delivery_address['city']); ?></p>
                        <p class="mb-1"><?php echo htmlspecialchars($delivery_address['zipcode']); ?></p>
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
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <h4><?php echo "Overall Total: ₱" . number_format($order['total_amount'], 2);?></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<style>
    .pagination .page-link {
        border-radius: 8px;
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

    .modal {
        backdrop-filter: blur(5px);
    }

    .modal-content {
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus {
        border-color: #FF8C00;
        box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25);
    }

    .btn-warning {
        background-color: #FF8C00;
        border-color: #FF8C00;
        color: white;
    }

    .btn-warning:hover {
        background-color: #e67e00;
        border-color: #e67e00;
        color: white;
    }

    .btn-outline-warning {
        color: #FF8C00;
        border-color: #FF8C00;
    }

    .btn-outline-warning:hover {
        background-color: #FF8C00;
        border-color: #FF8C00;
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>