<?php
$page_title = 'Adopt a Pet - ' . get_setting('site_name', 'Pawfect Pet Shop');
?>

<?php require_once 'views/layout/header.php'; ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Adopt a Pet</h1>
        <p class="lead text-muted">Find your perfect companion from our loving pets waiting for homes</p>
    </div>
    
    <!-- Filter Tabs -->
    <ul class="nav nav-pills justify-content-center mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo !isset($_GET['type']) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/pets">All Pets</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['type']) && $_GET['type'] === 'dogs' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/pets?type=dogs">Dogs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['type']) && $_GET['type'] === 'cats' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/pets?type=cats">Cats</a>
        </li>
    </ul>
    
    <div class="row">
        <?php if (empty($pets)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">No pets available for adoption</h3>
                <p class="text-muted">Check back soon for new arrivals!</p>
            </div>
        <?php else: ?>
            <?php foreach ($pets as $pet): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pet-card h-100">
                    <img src="<?php echo $pet['pet_image']; ?>" class="card-img-top" style="height: 300px; object-fit: cover;" alt="<?php echo $pet['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <?php echo $pet['name']; ?>
                            <span class="badge bg-primary"><?php echo ucfirst($pet['type']); ?></span>
                        </h5>
                        <div class="mb-3">
                            <div class="row text-muted small">
                                <div class="col-6">
                                    <i class="fas fa-venus-mars"></i> <?php echo ucfirst($pet['gender']); ?>
                                </div>
                                <div class="col-6">
                                    <i class="fas fa-birthday-cake"></i> <?php echo $pet['age']; ?> years
                                </div>
                            </div>
                            <div class="mt-2">
                                <strong>Breed:</strong> <?php echo $pet['breed']; ?>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>/pet/<?php echo $pet['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-heart"></i> Meet <?php echo $pet['name']; ?>
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <button onclick="adoptPet(<?php echo $pet['id']; ?>)" class="btn btn-outline-warning">
                                    <i class="fas fa-home"></i> Adopt Now
                                </button>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-warning">
                                    Login to Adopt
                                </a>
                            <?php endif; ?>
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
.pet-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.pet-image-container {
    position: relative;
    overflow: hidden;
}

.pet-overlay {
    background: rgba(0, 0, 0, 0.8);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pet-card:hover .pet-overlay {
    opacity: 1;
}

.pet-badges .badge {
    font-size: 0.7rem;
}

.pet-details {
    background: rgba(248, 249, 250, 0.8);
    padding: 0.75rem;
    border-radius: 8px;
    margin: 1rem 0;
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
