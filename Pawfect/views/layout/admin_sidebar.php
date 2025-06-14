<?php
// Get the current page from the URL
$currentPath = $_SERVER['REQUEST_URI'];
$currentPage = '';

// Simple path matching
if (strpos($currentPath, '/admin/pawducts') !== false) {
    $currentPage = 'products';
} elseif (strpos($currentPath, '/admin/orders') !== false) {
    $currentPage = 'orders';
} elseif (strpos($currentPath, '/admin/pets') !== false) {
    $currentPage = 'pets';
} elseif (strpos($currentPath, '/admin/pet-orders') !== false) {
    $currentPage = 'pet-orders';
} elseif (strpos($currentPath, '/admin/users') !== false) {
    $currentPage = 'users';
} elseif (strpos($currentPath, '/admin/settings') !== false) {
    $currentPage = 'settings';
} elseif (strpos($currentPath, '/admin') !== false) {
    $currentPage = 'dashboard';
}
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="<?php echo BASE_URL; ?>/public/uploads/logo/PawfectPetShopLogo.jpg" alt="Pawfect Logo" class="img-fluid mb-2" style="max-width: 100px;">
            <h5 class="mb-0">Admin Dashboard</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'products') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/pawducts">
                    <i class="fas fa-box me-2"></i>
                    Manage Pawducts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'orders') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/orders">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Manage Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'pets') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/pets">
                    <i class="fas fa-paw me-2"></i>
                    Manage Pets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'pet-orders') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/pet-orders">
                    <i class="fas fa-paw me-2"></i>
                    Pet Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'users') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/users">
                    <i class="fas fa-users me-2"></i>
                    Manage Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'settings') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/admin/settings">
                    <i class="fas fa-cog me-2"></i>
                    Settings
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