<?php
/**
 * Dev-server router: serve real files from public/ directly, hand everything else to Laravel.
 *
 * PHP's built-in server passes *every* request to the router script, so without this an
 * image request returns the HTML of the home page. Only needed for `php -S`.
 *
 *   php -S 0.0.0.0:8080 -t public scripts/dev/router.php
 */
$root = $_SERVER['DOCUMENT_ROOT'] ?: __DIR__ . '/../../public';
$uri  = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$file = realpath($root . $uri);

if ($uri !== '/' && $file && is_file($file) && str_starts_with($file, realpath($root))) {
    return false; // built-in server streams it with the right content type
}

require $root . '/index.php';
