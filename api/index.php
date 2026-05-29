<?php

echo "<h1>Filesystem Check</h1>";
echo "<p>Checking bootstrap/cache contents...</p>";
$dir = __DIR__ . '/../bootstrap/cache';
if (is_dir($dir)) {
    $files = scandir($dir);
    echo "<ul>";
    foreach ($files as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Directory $dir does not exist.</p>";
}

echo "<p>Checking if /tmp/bootstrap/cache is writable...</p>";
if (is_dir('/tmp/bootstrap/cache')) {
    echo "<p>/tmp/bootstrap/cache exists. Attempting to write...</p>";
    if (touch('/tmp/bootstrap/cache/test.txt')) {
        echo "<p>SUCCESS: Wrote to /tmp/bootstrap/cache/test.txt</p>";
    } else {
        echo "<p>FAILURE: Could not write to /tmp/bootstrap/cache/test.txt</p>";
    }
} else {
    echo "<p>/tmp/bootstrap/cache does not exist.</p>";
}
