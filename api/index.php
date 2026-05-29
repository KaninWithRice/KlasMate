<?php

/**
 * Vercel Bridge for Laravel
 * Handles the read-only filesystem by redirecting storage and cache to /tmp.
 * NO OUTPUT BEFORE BOOT (To prevent breaking file streams/downloads)
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
// Resolve ${APP_URL} interpolation if present
$appUrl = getenv('APP_URL') ?: 'https://klas-mate.vercel.app';
$googleRedirect = getenv('GOOGLE_REDIRECT_URI');
if (strpos($googleRedirect, '${APP_URL}') !== false) {
    putenv("GOOGLE_REDIRECT_URI=" . str_replace('${APP_URL}', $appUrl, $googleRedirect));
}

putenv('APP_CONFIG_CACHE=/tmp/storage/framework/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/storage/framework/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/storage/framework/cache/events.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// DIRECT DEBUG ROUTE (Bypasses Laravel)
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/debug-vercel') !== false) {
    header('Content-Type: text/plain');
    echo "VERCEL BRIDGE DIAGNOSTICS\n";
    echo "=========================\n";
    echo "APP_URL: " . getenv('APP_URL') . "\n";
    echo "FILESYSTEM_DISK: " . getenv('FILESYSTEM_DISK') . "\n";
    echo "GOOGLE_REDIRECT_URI: " . getenv('GOOGLE_REDIRECT_URI') . "\n";
    echo "AWS_BUCKET: " . getenv('AWS_BUCKET') . "\n";
    echo "PHP_VERSION: " . PHP_VERSION . "\n";
    
    echo "\nSTORAGE TEST:\n";
    try {
        $disk = \Storage::disk('s3');
        echo "Disk resolved successfully.\n";
        // Attempt to list files in root to see if bucket is reachable
        $files = $disk->files();
        echo "Successfully reached bucket. Found " . count($files) . " files.\n";
    } catch (\Throwable $e) {
        echo "STORAGE ERROR: " . $e->getMessage() . "\n";
    }
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

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $request = Illuminate\Http\Request::capture();

    // DIRECT DEBUG ROUTE (Bypasses Laravel Routing but uses bootstrapped app)
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/debug-vercel') !== false) {
        header('Content-Type: text/plain');
        echo "VERCEL BRIDGE DIAGNOSTICS\n";
        echo "=========================\n";
        echo "APP_URL: " . config('app.url') . "\n";
        echo "FILESYSTEM_DISK: " . config('filesystems.default') . "\n";
        echo "AWS_BUCKET: " . config('filesystems.disks.s3.bucket') . "\n";
        echo "AWS_URL: " . config('filesystems.disks.s3.url') . "\n";
        
        echo "\nSTORAGE TEST:\n";
        try {
            $files = \Illuminate\Support\Facades\Storage::disk('s3')->files();
            echo "Successfully reached bucket. Found " . count($files) . " files.\n";
            if (count($files) > 0) {
                echo "Example file: " . $files[0] . "\n";
                echo "Generated URL: " . \Illuminate\Support\Facades\Storage::disk('s3')->url($files[0]) . "\n";
            }
        } catch (\Throwable $e) {
            echo "STORAGE ERROR: " . $e->getMessage() . "\n";
        }
        exit;
    }

    $response = $kernel->handle($request);

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "LATEST BOOT ERROR:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine();
    exit;
}
