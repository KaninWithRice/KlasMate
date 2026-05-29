<?php

// HEARTBEAT
echo "<div style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc;'>Vercel Bridge Heartbeat: Active (PHP " . PHP_VERSION . ")</div>";

// Enable aggressive error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Shutdown function to catch silent fatals
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        echo "<div style='color:red; font-family:monospace; padding:10px; border:2px solid red; margin-top:10px;'>";
        echo "<strong>SHUTDOWN FATAL ERROR CAUGHT:</strong><br>";
        echo "Message: " . $error['message'] . "<br>";
        echo "File: " . $error['file'] . ":" . $error['line'];
        echo "</div>";
    }
});

echo "<p>STEP 1: Checking environment...</p>";

// Check for APP_KEY
if (!getenv('APP_KEY') && !isset($_ENV['APP_KEY'])) {
    echo "<p style='color:orange;'>WARNING: APP_KEY is not set. Laravel will likely crash.</p>";
} else {
    echo "<p style='color:green;'>SUCCESS: APP_KEY is detected.</p>";
}

echo "<p>STEP 2: Preparing writable directories...</p>";
$dirs = ['/tmp/storage/framework/views', '/tmp/storage/framework/sessions', '/tmp/storage/framework/cache', '/tmp/bootstrap/cache'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}
if (!file_exists('/tmp/database.sqlite')) touch('/tmp/database.sqlite');

echo "<p>STEP 3: Checking autoloader...</p>";
$autoloader = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloader)) {
    die("<p style='color:red;'>FATAL: vendor/autoload.php NOT FOUND. Did the build fail?</p>");
}
echo "<p style='color:green;'>SUCCESS: Autoloader found.</p>";

echo "<p>STEP 4: Booting Laravel (public/index.php)...</p>";

try {
    require __DIR__ . '/../public/index.php';
    echo "<p>STEP 5: Laravel execution finished.</p>";
} catch (\Throwable $e) {
    echo "<div style='color:red; font-family:monospace; padding:10px; border:2px solid red;'>";
    echo "<strong>THROWABLE CAUGHT:</strong><br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
