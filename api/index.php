<?php

// EXTREME DEBUGGING
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "<h1>DIAGNOSTIC START</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<!-- DEPLOYMENT_MARKER: LATEST_V6 -->";

// Ensure writable directories exist in /tmp
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            echo "<p>FAILED TO CREATE DIR: $dir</p>";
        }
    }
}

// Ensure SQLite database exists in /tmp
if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

// Check for APP_KEY
if (!getenv('APP_KEY') && !isset($_ENV['APP_KEY'])) {
    echo "<h2>ERROR: APP_KEY IS MISSING</h2>";
    echo "<p>Please add APP_KEY to Vercel Environment Variables.</p>";
}

echo "<p>Attempting to boot Laravel...</p>";

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h2>FATAL BOOT ERROR</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p>DIAGNOSTIC END</p>";
