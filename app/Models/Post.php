<?php

namespace App\Models;

use PDO;

class Post
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Получить ленту постов по условиям:
     * - сортировка по hotness DESC
     * - исключить посты, которые пользователь уже видел
     * - исключить посты с total_views > 1000
     *
     * @param string $userId - уникальный идентификатор пользователя
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getFeed(string $userId, int $limit = 20, int $offset = 0): array
    {
        $sql = "
        SELECT p.*
        FROM posts p
        WHERE p.total_views <= 1000
          AND p.id NOT IN (
              SELECT post_id FROM post_views WHERE user_id = :user_id
          )
        ORDER BY p.hotness DESC
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Отметить, что пользователь просмотрел пост
     *
     * @param string $userId
     * @param array $postIds
     * @return void
     */
    public function markPostsAsViewed(string $userId, array $postIds): void
    {
        if (empty($postIds)) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $placeholders = [];
        $params = [];

        foreach ($postIds as $postId) {
            $placeholders[] = "(?, ?, ?)";
            $params[] = $postId;
            $params[] = $userId;
            $params[] = $timestamp;
        }

        // Вставляем новые просмотры, пропуская дубликаты
        $sqlInsert = "
        INSERT INTO post_views (post_id, user_id, viewed_at)
        VALUES " . implode(',', $placeholders) . "
        ON CONFLICT(post_id, user_id) DO NOTHING
    ";

        $stmtInsert = $this->pdo->prepare($sqlInsert);
        $stmtInsert->execute($params);

        // Обновляем счетчики просмотров для постов, которые могли быть отмечены
        $inClause = implode(',', array_fill(0, count($postIds), '?'));
        $sqlUpdate = "UPDATE posts SET total_views = total_views + 1 WHERE id IN ($inClause)";
        $stmtUpdate = $this->pdo->prepare($sqlUpdate);
        $stmtUpdate->execute($postIds);
    }


    public function countFeed(string $userId): int
    {
        $sql = "
        SELECT COUNT(*) FROM posts p
        WHERE p.total_views <= 1000
          AND p.id NOT IN (
              SELECT post_id FROM post_views WHERE user_id = :user_id
          )
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
}
