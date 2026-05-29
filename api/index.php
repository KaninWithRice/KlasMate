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

// 3. Boot Laravel
try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Force writable paths
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    $request = \Illuminate\Http\Request::capture();

    // 🚀 DIRECT DIAGNOSTIC ROUTE (Runs after app boot)
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/debug-vercel') !== false) {
        header('Content-Type: text/plain');
        echo "VERCEL BRIDGE DIAGNOSTICS (BOOTSTRAPPED)\n";
        echo "========================================\n";
        echo "APP_URL: " . config('app.url') . "\n";
        echo "FILESYSTEM_DISK: " . config('filesystems.default') . "\n";
        echo "AWS_BUCKET: " . config('filesystems.disks.s3.bucket') . "\n";
        echo "AWS_URL: " . config('filesystems.disks.s3.url') . "\n";
        
        echo "\nSTORAGE TEST:\n";
        try {
            // Use fully qualified namespace to avoid "Class not found"
            $disk = \Illuminate\Support\Facades\Storage::disk('s3');
            $files = $disk->files();
            echo "SUCCESS: Reached bucket. Found " . count($files) . " files.\n";
            if (count($files) > 0) {
                echo "Example file: " . $files[0] . "\n";
                echo "Generated URL: " . $disk->url($files[0]) . "\n";
            }
        } catch (\Throwable $e) {
            echo "STORAGE ERROR: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
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
