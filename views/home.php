<?php require_once 'layout/header.php'; ?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Welcome to Pawfect Pet Shop</h1>
        <p class="lead mb-4">Find your perfect companion and everything they need to be happy and healthy</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <a href="<?php echo BASE_URL; ?>/pets" class="btn btn-light btn-lg me-3 mb-2">
                    <i class="fas fa-heart"></i> Adopt a Pet
                </a>
                <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-light btn-lg mb-2">
                    <i class="fas fa-shopping-bag"></i> Shop Products
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Featured Pets Section -->
    <section class="mb-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Pets Looking for Homes</h2>
            <p class="text-muted">These adorable pets are waiting for their forever families</p>
        </div>
        
        <div class="row">
            <?php foreach ($featuredPets as $pet): ?>
            <div class="col-md-4 mb-4">
                <div class="card pet-card h-100">
                    <img src="<?php echo $pet['pet_image']; ?>" class="card-img-top" style="height: 250px; object-fit: cover;" alt="<?php echo $pet['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $pet['name']; ?></h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-paw"></i> <?php echo ucfirst($pet['type']); ?> • 
                                <i class="fas fa-venus-mars"></i> <?php echo ucfirst($pet['gender']); ?> • 
                                <i class="fas fa-birthday-cake"></i> <?php echo $pet['age']; ?> years
                            </small><br>
                            <strong>Breed:</strong> <?php echo $pet['breed']; ?>
                        </p>
                        <div class="d-grid">
                            <a href="<?php echo BASE_URL; ?>/pet/<?php echo $pet['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-heart"></i> Meet <?php echo $pet['name']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/pets" class="btn btn-outline-primary btn-lg">
                View All Pets <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
    
    <!-- Featured Products Section -->
    <section>
        <div class="text-center mb-4">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Everything your pet needs for a happy and healthy life</p>
        </div>
        
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    <img src="<?php echo $product['product_image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo $product['name']; ?></h6>
                        <p class="card-text">
                            <span class="badge bg-secondary"><?php echo ucfirst($product['type']); ?></span><br>
                            <strong class="text-primary fs-5">$<?php echo number_format($product['price'], 2); ?></strong><br>
                            <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                        </p>
                        <div class="d-grid gap-2">
                            <?php if (isLoggedIn()): ?>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-primary btn-sm">
                                    Login to Purchase
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/product/<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-primary btn-lg">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
</div>

<?php require_once 'layout/footer.php'; ?>
