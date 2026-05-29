<?php

/**
 * Vercel Bridge for Laravel
 * Robust bootstrapper that ensures all core services are registered.
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

// 2. Set Environment for Vercel
$appUrl = getenv('APP_URL') ?: 'https://klas-mate.vercel.app';
$googleRedirect = getenv('GOOGLE_REDIRECT_URI');

// Manual fix for common interpolation issue
if (strpos($googleRedirect, '${APP_URL}') !== false) {
    $googleRedirect = str_replace('${APP_URL}', $appUrl, $googleRedirect);
    putenv("GOOGLE_REDIRECT_URI=$googleRedirect");
    $_ENV['GOOGLE_REDIRECT_URI'] = $googleRedirect;
}

putenv('APP_CONFIG_CACHE=/tmp/storage/framework/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/storage/framework/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/storage/framework/cache/events.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// DIRECT DEBUG ROUTE (Bypasses Laravel)
if (strpos($_SERVER['REQUEST_URI'], '/debug-vercel') !== false) {
    header('Content-Type: text/plain');
    echo "VERCEL BRIDGE DIAGNOSTICS\n";
    echo "=========================\n";
    echo "APP_URL: " . getenv('APP_URL') . "\n";
    echo "GOOGLE_REDIRECT_URI: " . getenv('GOOGLE_REDIRECT_URI') . "\n";
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
    echo "PHP_VERSION: " . PHP_VERSION . "\n";
    exit;
}

// 3. Boot Laravel
try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Force writable paths
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    // Handle the request using the Kernel (This ensures all core bindings like MaintenanceMode are set up)
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid #000; margin:20px;'>";
    echo "<h1>KlasMate: Boot Error</h1>";
    echo "<p style='color:red;'><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre style='background:#eee; padding:10px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
