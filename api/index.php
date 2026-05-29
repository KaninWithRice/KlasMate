<?php

/**
 * Vercel Bridge for Laravel
 * Manually registers essential providers to bypass read-only filesystem restrictions.
 */

// 1. Prepare Writable Filesystem
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}
if (!file_exists('/tmp/database.sqlite')) {
    @touch('/tmp/database.sqlite');
}

// 2. Set Environment for Vercel
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/framework/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/framework/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/framework/cache/events.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['BOOTSTRAP_CACHE_PATH'] = '/tmp/bootstrap/cache';

// 3. Boot Laravel with Manual Provider Injection
try {
    // We need to capture the app instance to manually register providers
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Manually register core providers that fail to load without packages.php
    $app->register(\Illuminate\View\ViewServiceProvider::class);
    $app->register(\Illuminate\Session\SessionServiceProvider::class);
    $app->register(\Illuminate\Cache\CacheServiceProvider::class);
    $app->register(\Illuminate\Routing\RoutingServiceProvider::class);
    $app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);

    $app->handleRequest(\Illuminate\Http\Request::capture());

} catch (\Throwable $e) {
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid #000; margin:20px;'>";
    echo "<h1>KlasMate: Boot Error</h1>";
    echo "<p style='color:red;'><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre style='background:#eee; padding:10px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
