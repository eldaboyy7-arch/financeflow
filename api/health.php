<?php
header('Content-Type: application/json');
try {
    echo json_encode([
        'status' => 'ok',
        'php_version' => PHP_VERSION,
        'pdo_drivers' => class_exists('PDO') ? PDO::getAvailableDrivers() : [],
        'db_host_loaded' => !empty(getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? ''),
        'app_key_loaded' => !empty(getenv('APP_KEY') ?: $_ENV['APP_KEY'] ?? ''),
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
