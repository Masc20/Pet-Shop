<?php require_once 'views/layout/header.php'; ?>

<div class="container py-5">
    <h1 class="fw-bold mb-4">Shopping Cart</h1>
    
    <?php if (empty($items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">Your cart is empty</h3>
            <p class="text-muted">Add some products to get started!</p>
            <a href="<?php echo BASE_URL; ?>/products" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Shop Now
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <?php foreach ($items as $item): ?>
                <div class="card mb-3">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <img src="<?php echo $item['product_image']; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="<?php echo $item['name']; ?>">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $item['name']; ?></h5>
                                <p class="card-text">
                                    <strong class="text-primary">$<?php echo number_format($item['price'], 2); ?></strong> each<br>
                                    <small class="text-muted">Stock: <?php echo $item['stock_quantity']; ?> available</small>
                                </p>
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <form method="POST" action="<?php echo BASE_URL; ?>/cart/update" class="d-flex align-items-center">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <label class="me-2">Qty:</label>
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" class="form-control me-2" style="width: 80px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <strong>Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong><br>
                                        <form method="POST" action="<?php echo BASE_URL; ?>/cart/remove" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total:</span>
                            <strong class="text-primary fs-4">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <div class="d-grid">
                            <form method="POST" action="<?php echo BASE_URL; ?>/cart/checkout">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card"></i> Checkout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>
