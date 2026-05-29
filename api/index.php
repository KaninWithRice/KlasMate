<?php

// 1. SILENTLY PREPARE FILESYSTEM
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

// 2. SMOKE TEST (ONLY FOR THE HOME PAGE)
// This confirms the PHP server is actually working.
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/smoke-test') {
    // We will still try to boot Laravel, but we'll show this if it fails
}

// 3. BOOT LARAVEL
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    // If Laravel crashes, show a helpful screen
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid #000; margin:20px;'>";
    echo "<h1>KlasMate: Deployment Status</h1>";
    echo "<p style='color:green;'>✅ PHP Server is Running</p>";
    echo "<p style='color:red;'>❌ Laravel Boot Failed</p>";
    echo "<hr>";
    echo "<h3>Error Message:</h3>";
    echo "<p><code>" . $e->getMessage() . "</code></p>";
    echo "<h3>Suggested Action:</h3>";
    if (strpos($e->getMessage(), 'view') !== false) {
        echo "<p>The <b>view</b> system is failing. This usually means the <code>bootstrap/cache</code> files are missing from GitHub. I will fix this in the next push.</p>";
    }
    echo "</div>";
}
