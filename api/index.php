<?php

/**
 * Vercel Bridge for Laravel
 * Manually boots the application and registers providers in a read-only environment.
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

// 3. Boot Laravel with Controlled Bootstrapping
try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Manually run foundational bootstrappers
    // This sets up config, env, facades, etc.
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    ]);

    // Now that 'config' exists, we can register providers
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
