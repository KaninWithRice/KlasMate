<?php

/**
 * Vercel Bridge for Laravel
 * This script handles the read-only filesystem by redirecting storage and cache to /tmp.
 */

// 1. Setup writable directories
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Synchronize bootstrap cache
// Laravel needs services.php and packages.php to know which providers to load.
$srcCache = __DIR__ . '/../bootstrap/cache';
$dstCache = '/tmp/bootstrap/cache';

foreach (['services.php', 'packages.php'] as $file) {
    if (file_exists("$srcCache/$file")) {
        copy("$srcCache/$file", "$dstCache/$file");
    }
}

// 3. Ensure SQLite database exists
if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

// 4. Force environment variables for Vercel
// These ensure Laravel uses the writable paths we just created.
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/framework/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/framework/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/framework/cache/events.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// 5. Boot Laravel
require __DIR__ . '/../public/index.php';
