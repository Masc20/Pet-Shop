<?php
// Get the current page from the URL
$currentPath = $_SERVER['REQUEST_URI'];
$currentPage = '';

// Simple path matching
if (strpos($currentPath, '/user/dashboard') !== false) {
    $currentPage = 'dashboard';
} elseif (strpos($currentPath, '/user/products') !== false) {
    $currentPage = 'products';
} elseif (strpos($currentPath, '/user/orders') !== false) {
    $currentPage = 'orders';
} elseif (strpos($currentPath, '/user/pet-orders') !== false) {
    $currentPage = 'pet-orders';
} elseif (strpos($currentPath, '/cart') !== false) {
    $currentPage = 'cart';
} elseif (strpos($currentPath, '/profile') !== false) {
    $currentPage = 'profile';
}
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="<?php echo BASE_URL; ?>/public/uploads/logo/PawfectPetShopLogo.jpg" alt="Pawfect Logo" class="img-fluid mb-2" style="max-width: 100px;">
            <h5 class="mb-0">User Dashboard</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/user/dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'profile') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/profile">
                    <i class="fas fa-user me-2"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'products') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/user/products">
                    <i class="fas fa-box me-2"></i>
                    My Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'orders') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/user/orders">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Manage Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'cart') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/cart">
                    <i class="fas fa-shopping-cart me-2"></i>
                    My Cart
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="<?php echo BASE_URL; ?>/logout">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    .sidebar {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        height: calc(100vh - 56px);
        width: 230px;
        left: 0;
        z-index: 1;
        overflow-y: auto;
    }

    .nav-link {
        color: #333;
        padding: 0.6rem 1rem;
        border-radius: 0.25rem;
        margin: 0.1rem 0.5rem;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .nav-link:hover {
        background-color: rgba(255, 140, 0, 0.1);
        color: #FF8C00;
    }

    .nav-link.active {
        background-color: #FF8C00;
        color: white;
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    .nav-link.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Adjust main content to account for fixed sidebar */
    main {
        margin-left: 16.666667%; /* col-md-2 width */
        padding-top: 56px; /* Height of the header */
    }

    @media (max-width: 767.98px) {
        .sidebar {
            position: static;
            height: auto;
            top: 0;
        }
        main {
            margin-left: 0;
            padding-top: 0;
        }
    }
</style> 