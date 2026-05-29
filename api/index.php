<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!-- DEPLOYMENT_MARKER: LATEST_V7 -->";

// Ensure writable directories exist in /tmp
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Copy existing cache files to /tmp so Laravel has a writable starting point
$srcCache = __DIR__ . '/../bootstrap/cache';
$dstCache = '/tmp/bootstrap/cache';

if (file_exists("$srcCache/services.php")) {
    copy("$srcCache/services.php", "$dstCache/services.php");
}
if (file_exists("$srcCache/packages.php")) {
    copy("$srcCache/packages.php", "$dstCache/packages.php");
}

// Ensure SQLite database exists in /tmp
if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

// Forward Vercel requests to normal index.php
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "LATEST BOOT ERROR:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
}
