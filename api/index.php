<?php

// Enable raw error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure writable directories exist in /tmp
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}

if (!is_dir('/tmp/bootstrap/cache')) {
    mkdir('/tmp/bootstrap/cache', 0755, true);
}

// Check for APP_KEY and print status (for debugging only)
if (!getenv('APP_KEY') && !isset($_ENV['APP_KEY'])) {
    echo "<h1>Configuration Error</h1>";
    echo "<p><strong>Your APP_KEY is missing.</strong></p>";
    echo "<p>Please go to Vercel Dashboard > Settings > Environment Variables and add the <code>APP_KEY</code> from your local .env file.</p>";
    exit;
} else {
    // Hidden debug info to confirm key is present without leaking it
    echo "<!-- APP_KEY is set -->";
}

// Define a custom exception handler to catch errors BEFORE Laravel's handler crashes
set_exception_handler(function ($e) {
    echo "<h1>Caught Root Exception</h1>";
    echo "<p><strong>Class:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
});

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';
