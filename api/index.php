<?php

/**
 * Vercel Bridge for Laravel
 * Enforces writable paths on the Application instance for Vercel compatibility.
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

    // FORCE WRITABLE PATHS ON THE APP INSTANCE
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    // Manually run foundational bootstrappers
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    ]);

    // Register essential providers
    $providers = [
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \Illuminate\Log\LogServiceProvider::class,
        \Illuminate\Events\EventServiceProvider::class,
        \Illuminate\Database\DatabaseServiceProvider::class,
        \Illuminate\Encryption\EncryptionServiceProvider::class,
        \Illuminate\Cookie\CookieServiceProvider::class,
        \Illuminate\Session\SessionServiceProvider::class,
        \Illuminate\View\ViewServiceProvider::class,
        \Illuminate\Routing\RoutingServiceProvider::class,
        \Illuminate\Auth\AuthServiceProvider::class,
        \Illuminate\Cache\CacheServiceProvider::class,
        \Illuminate\Pipeline\PipelineServiceProvider::class,
        \Illuminate\Translation\TranslationServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,
        \App\Providers\AppServiceProvider::class,
    ];

    foreach ($providers as $provider) {
        $app->register($provider);
    }

    $app->handleRequest(\Illuminate\Http\Request::capture());

} catch (\Throwable $e) {
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid #000; margin:20px;'>";
    echo "<h1>KlasMate: Boot Error</h1>";
    echo "<p style='color:red;'><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre style='background:#eee; padding:10px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
