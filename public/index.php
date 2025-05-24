<?php
// Load global config only once
require_once(__DIR__ . '/../config/app.php');

// Optional: Load database or session configs
// require_once(APP_ROOT . '/config/database.php');

// Route logic
require_once(APP_ROOT . '/router/web.php');
