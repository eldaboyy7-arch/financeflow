<?php

// Vercel Serverless Entry Point for Laravel
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
    $storagePath . '/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

putenv('APP_STORAGE=' . $storagePath);
putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');

if (!file_exists(__DIR__ . '/../backend/public/index.php')) {
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'message' => 'File backend tidak ditemukan di dalam paket Vercel.',
        'dir' => __DIR__,
    ]);
    exit;
}

require __DIR__ . '/../backend/public/index.php';
