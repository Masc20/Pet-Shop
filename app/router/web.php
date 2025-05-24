<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'products':
        require_once(APP_ROOT . '/views/pages/ProductPage.php');
        break;
    case 'login':
        require_once(APP_ROOT . '/views/pages/LoginPage.php');
        break;
    default:
        require_once(APP_ROOT . '/views/pages/HomePage.php');
}
