<?php

namespace App\Controllers;

use App\Models\Post;
use App\Services\DatabaseSeeder;
use App\Services\ViewService;
use PDO;
use PDOException;

class PostController
{
    protected Post $postModel;
    protected DatabaseSeeder $seeder;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->postModel = new Post($pdo);
        $this->seeder = new DatabaseSeeder($pdo);
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $userId = $_GET['user_id'] ?? 1;
        if (!$userId) {
            http_response_code(400);
            echo ViewService::render('error', ['message' => 'user_id is required']);
            return;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        try {
            // Получаем посты с учетом лимита и офсета
            $postsData = $this->postModel->getFeed($userId, $limit, $offset);

            $postIds = array_column($postsData, 'id');
            $this->postModel->markPostsAsViewed($userId, $postIds);

            // Получаем общее количество постов для пагинации
            $totalPosts = $this->postModel->countFeed($userId);

            $lastPage = (int)ceil($totalPosts / $limit);

            $posts = [
                'data' => $postsData,
                'current_page' => $page,
                'last_page' => $lastPage ?: 1,
                'prev_page' => $page > 1 ? $page - 1 : null,
                'next_page' => $page < $lastPage ? $page + 1 : null,
            ];
        } catch (PDOException $e) {
            // Если ошибка — таблица posts не существует
            if (str_contains($e->getMessage(), 'no such table')) {
                $posts = [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'prev_page' => null,
                    'next_page' => null,
                ];
            } else {
                // Пробрасываем остальные ошибки дальше
                throw $e;
            }
        }

        echo ViewService::render('home', [
            'title' => 'Лента постов',
            'posts' => $posts,
        ]);
    }

    /**
     * @return void
     */
    public function markViewed(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;

        // Можно принимать либо 'post_id' (int), либо 'post_ids' (массив)
        if (isset($input['post_ids']) && is_array($input['post_ids'])) {
            $postIds = array_map('intval', $input['post_ids']);
        } elseif (isset($input['post_id'])) {
            $postIds = [(int)$input['post_id']];
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'post_id or post_ids are required']);
            return;
        }

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'user_id is required']);
            return;
        }

        $this->postModel->markPostsAsViewed($userId, $postIds);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    /**
     * @return void
     */
    public function fillDatabase(): void
    {
        $this->seeder->seedPosts();
        echo json_encode(['status' => 'ok', 'message' => 'Database filled with fake data']);
    }

    /**
     * @return void
     */
    public function clearDatabase(): void
    {
        $this->seeder->resetDatabase();
        echo json_encode(['status' => 'ok', 'message' => 'Database cleared']);
    }
}