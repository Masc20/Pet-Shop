<?php require_once 'views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo $product['product_image']; ?>" class="img-fluid rounded shadow" alt="<?php echo $product['name']; ?>">
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold"><?php echo $product['name']; ?></h1>
            <div class="mb-3">
                <span class="badge bg-secondary fs-6"><?php echo ucfirst($product['type']); ?></span>
            </div>
            
            <div class="mb-4">
                <h3 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h3>
                <p class="text-muted">
                    Stock: <?php echo $product['stock_quantity']; ?> available
                    <?php if ($product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0): ?>
                        <span class="text-warning">(Low Stock)</span>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <div class="d-grid gap-2">
                <?php if (isLoggedIn() && $product['stock_quantity'] > 0): ?>
                    <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-lg">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                <?php elseif (!isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="btn btn-primary btn-lg">
                        Login to Purchase
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg" disabled>
                        Out of Stock
                    </button>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
