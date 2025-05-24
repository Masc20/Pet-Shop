<?php
include_once(__DIR__ . '/../../../config/app.php');
include_once(APP_ROOT . '/../app/helpers/auth.php');
?>

<style>
    nav {
        font-family: Arial, sans-serif;
        background: #333;
        color: #fff;
        padding: 10px 0;
        text-align: center;
    }

    nav a {
        color: #fff;
        margin: 0 20px;
        text-decoration: none;
        font-weight: bold;
    }

    nav a:hover {
        color: #ddd;
    }

    .nav-right {
        float: right;
        margin-right: 20px;
    }
</style>

<nav>
    <a href="<?= BASE_URL ?>/app/views/pages/HomePage.php">Home</a>
    <a href="<?= BASE_URL ?>/app/views/pages/ProductPage.php">Shop</a>
    <a href="#">Adopt</a>
    <a href="#">Contact</a>
    <a href="#">About</a>

    <div class="nav-right">
        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/app/views/loginPage/BawlangUnsani.php">Sign In</a>
            <a href="<?= BASE_URL ?>/app/views/loginPage/BawlangUnsani.php">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>