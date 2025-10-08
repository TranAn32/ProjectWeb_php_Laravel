<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    // Let PHP's built-in server handle the static file
    return false;
}

// Fallback to Laravel front controller
require __DIR__ . '/index.php';

