<?php

// Enable raw error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define a global exception handler that prints RAW text and EXITS.
// This prevents Laravel from ever trying to use its own crashing error handler.
set_exception_handler(function ($e) {
    header('Content-Type: text/plain');
    echo "ROOT EXCEPTION CAUGHT\n";
    echo "Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString();
    exit;
});

// Ensure writable directories exist in /tmp
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}
if (!is_dir('/tmp/bootstrap/cache')) {
    mkdir('/tmp/bootstrap/cache', 0755, true);
}

// Check for APP_KEY
if (!getenv('APP_KEY') && !isset($_ENV['APP_KEY'])) {
    die("ERROR: APP_KEY is not set in Vercel environment variables.");
}

// Boot Laravel
require __DIR__ . '/../public/index.php';
