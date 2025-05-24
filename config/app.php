<?php
define('APP_ROOT', dirname(__DIR__) . '/app');

// config/app.php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . '://' . $host . '/Pet-Shop-main');
