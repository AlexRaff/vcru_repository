<?php

use App\Controllers\PostController;

$pdo = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

return [
    'GET' => [
        '/' => function () use ($pdo) {
            $controller = new PostController($pdo);
            $controller->index();
        },
    ],
    'POST' => [
        '/api/posts/viewed' => function () use ($pdo) {
            $controller = new PostController($pdo);
            $controller->markViewed();
        },
        '/api/posts/seed' => function () use ($pdo) {
            $controller = new PostController($pdo);
            $controller->fillDatabase();
        },
        '/api/posts/clear' => function () use ($pdo) {
            $controller = new PostController($pdo);
            $controller->clearDatabase();
        },
    ],
];
