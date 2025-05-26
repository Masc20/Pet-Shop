<?php require_once 'views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header gradient-bg text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Profile</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo BASE_URL . $user['avatar']; ?>" alt="Avatar" class="rounded-circle mb-3" style="width: 90px; height: 90px; object-fit: cover; border: 3px solid #FFD700;">
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                    <?php endif; ?>
                    <h5><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h5>
                    <p class="text-muted"><?php echo $user['email']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
                
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deliveryAddressModal">
                <i class="fas fa-map-marker-alt"></i> Manage Delivery Address
            </button>
            </div>
            
            <!-- Orders Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">My Orders</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <p class="text-muted">No orders yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Shipped Date</th>
                                        <th>Delivery Address</th>
                                        <th>Payment</th>
                                        <th>Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['status'] === 'delivered' ? 'success' : 
                                                    ($order['status'] === 'pending' ? 'warning' : ($order['status'] === 'shipped' ? 'info' : 'primary')); 
                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $order['order_date'] ? date('M d, Y', strtotime($order['order_date'])) : '-'; ?></td>
                                        <td><?php echo $order['shipped_date'] ? date('M d, Y', strtotime($order['shipped_date'])) : '-'; ?></td>
                                        <td>
                                            <?php if (!empty($order['delivery_address'])): ?>
                                                <?php $addr = $order['delivery_address']; ?>
                                                <small><?php echo htmlspecialchars($addr['street'] . ', ' . $addr['city'] . ', ' . $addr['barangay'] . ' ' . $addr['zipcode']); ?></small>
                                            <?php else: ?>
                                                <small>-</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $order['payment_method'] ?? '-'; ?></td>
                                        <td>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <div class="d-flex align-items-center mb-2">
                                                    <img src="<?php echo BASE_URL . $item['product_image']; ?>" alt="<?php echo $item['name']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 8px;">
                                                    <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Delivery Address Modal -->
<div class="modal fade" id="deliveryAddressModal" tabindex="-1" aria-labelledby="deliveryAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveryAddressModalLabel">Delivery Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo BASE_URL; ?>/profile/update-address">
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars(isset($delivery_address['city']) ? $delivery_address['city'] : ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                        <input type="text" class="form-control" id="barangay" name="barangay" value="<?php echo htmlspecialchars(isset($delivery_address['barangay']) ? $delivery_address['barangay'] : ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlspecialchars(isset($delivery_address['street']) ? $delivery_address['street'] : ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="zipcode" class="form-label">Zipcode</label>
                        <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?php echo htmlspecialchars(isset($delivery_address['zipcode']) ? $delivery_address['zipcode'] : ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
