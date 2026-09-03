<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'php_version' => PHP_VERSION,
    'pdo_drivers' => extension_loaded('pdo') ? PDO::getAvailableDrivers() : [],
    'db_host_loaded' => !empty(getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? ''),
    'app_key_loaded' => !empty(getenv('APP_KEY') ?: $_ENV['APP_KEY'] ?? ''),
]);
