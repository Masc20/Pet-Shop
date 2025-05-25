<?php
$page_title = 'Products - ' . get_setting('site_name', 'Pawfect Pet Shop');
?>

<?php require_once 'views/layout/header.php'; ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Pet Products</h1>
        <p class="lead text-muted">Everything your furry friends need to stay happy and healthy</p>
    </div>
    
    <!-- Filter Tabs -->
    <ul class="nav nav-pills justify-content-center mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo !isset($_GET['type']) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/products">All Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['type']) && $_GET['type'] === 'foods' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/products?type=foods">Pet Foods</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['type']) && $_GET['type'] === 'accessories' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/products?type=accessories">Accessories</a>
        </li>
    </ul>
    
    <div class="row">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">No products available</h3>
                <p class="text-muted">Check back soon for new arrivals!</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card h-100">
                    <img src="<?php echo $product['product_image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo $product['name']; ?></h6>
                        <p class="card-text">
                            <span class="badge bg-secondary"><?php echo ucfirst($product['type']); ?></span><br>
                            <strong class="text-primary fs-5">$<?php echo number_format($product['price'], 2); ?></strong><br>
                            <small class="text-muted">
                                Stock: <?php echo $product['stock_quantity']; ?>
                                <?php if ($product['stock_quantity'] == 0): ?>
                                    <span class="text-danger">(Out of Stock)</span>
                                <?php elseif ($product['stock_quantity'] <= 5): ?>
                                    <span class="text-warning">(Low Stock)</span>
                                <?php endif; ?>
                            </small>
                        </p>
                        <div class="d-grid gap-2">
                            <?php if (isLoggedIn() && $product['stock_quantity'] > 0): ?>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php elseif (!isLoggedIn()): ?>
                                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-primary btn-sm">
                                    Login to Purchase
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/product/<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.product-image-container {
    position: relative;
    overflow: hidden;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.add-to-cart:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: var(--primary-orange);
}

.pagination .page-link:hover {
    background: var(--primary-orange);
    color: white;
}

.pagination .page-item.active .page-link {
    background: var(--primary-orange);
    border-color: var(--primary-orange);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const originalText = this.innerHTML;
            
            // Show loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            this.disabled = true;
            
            // Create form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            formData.append('_token', '<?php echo csrf_token(); ?>');
            
            fetch('<?php echo url('/cart/add'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        cartCount.style.display = data.cart_count > 0 ? 'inline' : 'none';
                    }
                    
                    // Show success message
                    showAlert(data.message, 'success');
                    
                    // Change button text temporarily
                    this.innerHTML = '<i class="fas fa-check me-2"></i>Added!';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                        this.disabled = false;
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to add product to cart', 'error');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });
});
</script>
