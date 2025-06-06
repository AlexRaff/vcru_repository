<?php

namespace App\Services;

use PDO;

class DatabaseSeeder
{
    protected PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return void
     */
    public function resetDatabase(): void
    {
        // Удаляем таблицы, если существуют
        $this->pdo->exec("DROP TABLE IF EXISTS posts");
        $this->pdo->exec("DROP TABLE IF EXISTS post_views");

        // Создаём таблицу постов
        $this->pdo->exec("
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                created_at TEXT NOT NULL,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                hotness INTEGER NOT NULL,
                total_views INTEGER NOT NULL DEFAULT 0
            )
        ");

        // Таблица учёта просмотров постов пользователями
        $this->pdo->exec("
            CREATE TABLE post_views (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER NOT NULL,
                user_id TEXT NOT NULL,
                viewed_at TEXT NOT NULL,
                UNIQUE(post_id, user_id),
                FOREIGN KEY(post_id) REFERENCES posts(id)
            )
        ");
    }

    /**
     * @param int $count
     * @return void
     */
    public function seedPosts(int $count = 3000): void
    {
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare("
            INSERT INTO posts (created_at, title, content, hotness) VALUES (:created_at, :title, :content, :hotness)
        ");

        for ($i = 1; $i <= $count; $i++) {
            $createdAt = date('Y-m-d H:i:s', strtotime("-" . rand(0, 365) . " days"));
            $title = "Заголовок поста №" . $i;
            $content = "Это содержимое поста №" . $i . ". " . $this->generateRandomText();
            $hotness = rand(1, 10000);

            $stmt->execute([
                ':created_at' => $createdAt,
                ':title' => $title,
                ':content' => $content,
                ':hotness' => $hotness,
            ]);
        }

        $this->pdo->commit();
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateRandomText(int $length = 100): string
    {
        $words = explode(' ', "рыба море океан волна плавник сеть крючок улов рыбак лодка берег глубина");
        $text = [];
        for ($i = 0; $i < $length; $i++) {
            $text[] = $words[array_rand($words)];
        }
        return implode(' ', $text);
    }
}
