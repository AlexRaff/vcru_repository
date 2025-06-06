<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$routes = require __DIR__ . '/../routes/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    if (!isset($routes[$method][$path])) {
        http_response_code(404);
        echo "Error: Route not found";
        exit;
    }

    $handler = $routes[$method][$path];

    if (is_callable($handler)) {
        $handler();
    } else {
        throw new Exception("Invalid route handler");
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
