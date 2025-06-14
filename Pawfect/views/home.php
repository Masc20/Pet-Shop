<?php require_once 'layout/header.php'; ?>

<div class="hero-section text-center" style="background: linear-gradient(135deg, #FF8C00 60%, #FFD700 100%); color: #fff; padding: 60px 0 40px 0; position: relative;">
    <div class="container">
        <?php if (getSetting('site_logo')): ?>
            <img src="<?php echo BASE_URL . getSetting('site_logo'); ?>" alt="<?php echo getSetting('brand_name', 'Pawfect Pet Shop'); ?> Logo" class="hero-logo-img">
        <?php else: ?>
            <img src="<?php echo BASE_URL; ?>/public/PawfectPetShopLogo.jpg" alt="<?php echo getSetting('brand_name', 'Pawfect Pet Shop'); ?> Logo" class="hero-logo-img">
        <?php endif; ?>
        <h1 class="display-4 fw-bold mb-3" style="font-family: 'Quicksand', Nunito, sans-serif; letter-spacing: 2px;">Welcome to <?php echo getSetting('brand_name', 'Pawfect Pet Shop'); ?></h1>
        <p class="lead mb-4" style="font-size: 1.3rem;">Find your perfect companion and everything they need to be happy and healthy</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <a href="<?php echo BASE_URL; ?>/pets" class="btn btn-lg fw-bold me-3 mb-2" style="background: #fff; color: #FF8C00; border-radius: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); font-size: 1.1rem;">
                    <i class="fas fa-heart"></i> Adopt a Pet
                </a>
                <a href="<?php echo BASE_URL; ?>/pawducts" class="btn btn-lg fw-bold mb-2" style="background: #FFD700; color: #fff; border-radius: 30px; font-size: 1.1rem;">
                    <i class="fas fa-shopping-bag"></i> Shop Pawducts
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Featured Pets Section -->
    <section class="mb-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #FF8C00; font-family: 'Quicksand', Nunito, sans-serif;">Pets Looking for Homes</h2>
            <p class="text-muted">These adorable pets are waiting for their forever families</p>
        </div>
        <div class="row">
            <?php foreach ($featuredPets as $pet): ?>
            <div class="col-md-4 mb-4">
                <div class="card pet-card h-100 shadow rounded-4 border-0" style="background: #fff8e1;">
                    <img src="<?php echo BASE_URL . $pet['pet_image']; ?>" class="card-img-top rounded-circle mx-auto mt-3" style="height: 180px; width: 180px; object-fit: cover; border: 4px solid #FFD700; background: #fff;" alt="<?php echo $pet['name']; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold" style="color: #FF8C00; letter-spacing: 1px;">🐾 <?php echo $pet['name']; ?></h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-paw"></i> <?php echo ucfirst($pet['type']); ?> • 
                                <i class="fas fa-venus-mars"></i> <?php echo ucfirst($pet['gender']); ?> • 
                                <i class="fas fa-birthday-cake"></i> <?php echo $pet['age']; ?> years
                                <br>
                                <i class="fas fa-calendar"></i> Born on <?php echo date('M d, Y', strtotime($pet['birthday'])); ?>
                            </small><br>
                            <strong>Breed:</strong> <?php echo $pet['breed']; ?>
                        </p>
                        <div class="d-grid">
                            <a href="<?php echo BASE_URL; ?>/pet/<?php echo $pet['id']; ?>" class="btn btn-warning rounded-pill fw-bold">
                                <i class="fas fa-heart"></i> Meet <?php echo $pet['name']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/pets" class="btn btn-outline-warning btn-lg rounded-pill fw-bold" style="color: #FF8C00; border: 2px solid #FFD700;">
                View All Pets <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
    
    <!-- Featured Products Section -->
    <section>
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #FF8C00; font-family: 'Quicksand', Nunito, sans-serif;">Featured Products</h2>
            <p class="text-muted">Everything your pet needs for a happy and healthy life</p>
        </div>
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100 shadow rounded-4 border-0" style="background: #fff8e1;">
                    <img src="<?php echo BASE_URL . $product['product_image']; ?>" class="card-img-top rounded-circle mx-auto mt-3" style="height: 120px; width: 120px; object-fit: cover; border: 4px solid #FF8C00; background: #fff;" alt="<?php echo $product['name']; ?>">
                    <div class="card-body text-center">
                        <h6 class="card-title fw-bold" style="color: #FF8C00; letter-spacing: 1px;">🛒 <?php echo $product['name']; ?></h6>
                        <p class="card-text text-muted">
                            <span class="badge bg-secondary" style="font-size: 0.9rem;"> <?php echo ucfirst($product['type']); ?> </span><br>
                            <strong class="text-primary fs-5">₱<?php echo number_format($product['price'], 2); ?></strong><br>
                            <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                        </p>
                        <div class="d-grid gap-2">
                            <?php if (isLoggedIn() && $product['stock_quantity'] > 0): ?>
                                <button onclick="showAddToCartModal(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm rounded-pill fw-bold">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php elseif (!isLoggedIn()): ?>
                                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
                                    Login to Purchase
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm rounded-pill fw-bold" disabled>
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/product/<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/pawducts" class="btn btn-outline-warning btn-lg rounded-pill fw-bold" style="color: #FF8C00; border: 2px solid #FFD700;">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to add <span id="productName" class="fw-bold"></span> to your cart?</p>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" min="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAddToCart">
                    <i class="fas fa-cart-plus me-2"></i> Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Added to Cart Success Modal -->
<div class="modal fade" id="addedToCartModal" tabindex="-1" aria-labelledby="addedToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addedToCartModalLabel">Item Added to Cart!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p>The product has been successfully added to your shopping cart.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="<?php echo BASE_URL; ?>/cart" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> View Cart</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Add this style block or integrate into your existing CSS */
.hero-logo-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    background: #fff;
    margin-bottom: 20px;
    border: 4px solid #fff3e0;
    object-fit: cover;
}
</style>

<script>
let bsAddToCartModal;
let bsAddedToCartModal;
let currentProductId = null;

function showAddToCartModal(productId) {
    const button = event.target.closest('button');
    const productName = button.closest('.card').querySelector('.card-title').textContent.replace('🛒 ', '');
    document.getElementById('productName').textContent = productName;
    document.getElementById('quantity').value = 1;
    currentProductId = productId;
    bsAddToCartModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const addToCartModal = document.getElementById('addToCartModal');
    const addedToCartModal = document.getElementById('addedToCartModal');

    // Initialize Bootstrap modals
    bsAddToCartModal = new bootstrap.Modal(addToCartModal);
    bsAddedToCartModal = new bootstrap.Modal(addedToCartModal);

    // Handle modal close buttons
    document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal === addToCartModal) {
                bsAddToCartModal.hide();
            } else if (modal === addedToCartModal) {
                bsAddedToCartModal.hide();
            }
        });
    });

    // Handle Continue Shopping button
    document.querySelector('#addedToCartModal .btn-secondary').addEventListener('click', function() {
        bsAddedToCartModal.hide();
    });

    // Handle Confirm Add to Cart button
    document.getElementById('confirmAddToCart').addEventListener('click', function() {
        if (!currentProductId) return;

        const quantity = document.getElementById('quantity').value;
        const formData = new FormData();
        formData.append('product_id', currentProductId);
        formData.append('quantity', quantity);

        fetch('<?php echo BASE_URL; ?>/cart/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bsAddToCartModal.hide();
                bsAddedToCartModal.show();
                
                // Update cart count if it exists
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                    cartCount.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
                }
            } else {
                alert(data.message || 'Failed to add item to cart');
                bsAddToCartModal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the item to your cart');
            bsAddToCartModal.hide();
        });
    });
});
</script>

<?php require_once 'layout/footer.php'; ?>
