<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-header gradient-bg text-white">
                    <h6 class="mb-0">Admin Menu</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo BASE_URL; ?>/admin" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/pets" class="list-group-item list-group-item-action">
                        <i class="fas fa-paw"></i> Manage Pets
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/products" class="list-group-item list-group-item-action">
                        <i class="fas fa-box"></i> Manage Products
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/orders" class="list-group-item list-group-item-action active">
                        <i class="fas fa-shopping-cart"></i> Manage Orders
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/users" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/settings" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-10">
            <h1 class="fw-bold mb-4">Manage Orders</h1>
            <!-- <div class="table-responsive"> -->
                <div class="card">

                    <div class="card-body">

                        <table class="table table-striped" style="z-index: 11110;">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                        <td><?php echo $order['email']; ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class=" badge bg-<?php
                                                                    echo $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'pending' ? 'warning' : ($order['status'] === 'cancelled' ? 'danger' : 'primary'));
                                                                    ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    Update Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="status" value="pending">
                                                            <button type="submit" class="dropdown-item">Pending</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="status" value="processing">
                                                            <button type="submit" class="dropdown-item">Processing</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="status" value="shipped">
                                                            <button type="submit" class="dropdown-item">Shipped</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="status" value="delivered">
                                                            <button type="submit" class="dropdown-item">Delivered</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Cancel this order?')">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger">Cancel Order</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                            <button class="btn btn-sm btn-outline-info" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewOrderDetails(orderId) {
        // You can implement order details modal here
        alert('Order details for Order #' + orderId + ' - Feature coming soon!');
    }
</script>

<?php require_once 'views/layout/footer.php'; ?>