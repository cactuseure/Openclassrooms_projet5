<?php

namespace App\Repository;

use App\Core\Db;
use App\Entity\Comment;
use DateTimeImmutable;
use Exception;
use PDO;

class CommentRepository
{
    private ?\PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function createComment(Comment $comment): void
    {
        $db = Db::getInstance();

        $sql = "INSERT INTO comments (content, author_id, created_at, parent_id, post_id, is_approved)
            VALUES (:content, :author, :created_at, :parent_id, :post_id, :is_approved)";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':content', $comment->getContent());
        $stmt->bindValue(':author', $comment->getAuthorId());
        $stmt->bindValue(':created_at', $comment->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':parent_id', $comment->getParentId());
        $stmt->bindValue(':post_id', $comment->getPostId());
        $stmt->bindValue(':is_approved', $comment->isApproved());

        $stmt->execute();
    }

    /**
     * @throws Exception
     */
    public function getAllComments(): array
    {
        $query = $this->db->query('SELECT * FROM comments');
        $results = $query->fetchAll();
        $comments = [];
        foreach ($results as $result) {
            $comment = new Comment(
                $result['id'],
                $result['content'],
                $result['author_id'],
                new DateTimeImmutable($result['created_at']),
                $result['parent_id'],
                $result['post_id'],
                $result['is_approved']
            );
            $comments[] = $comment;
        }

        return $comments;
    }

    public function getApprovedComments(): array
    {
        $query = $this->db->query('SELECT * FROM comments WHERE is_approved = 1');
        $results = $query->fetchAll();
        $comments = [];
        foreach ($results as $result) {
            $comment = new Comment(
                $result['id'],
                $result['content'],
                $result['author_id'],
                new DateTimeImmutable($result['created_at']),
                $result['parent_id'],
                $result['post_id'],
                $result['is_approved']
            );
            $comments[] = $comment;
        }

        return $comments;
    }



    public function getCommentsByPostId(int $postId): array
    {
        $query = $this->db->prepare('SELECT * FROM comments WHERE post_id = :post_id AND is_approved = 1');
        $query->execute(['post_id' => $postId]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];
        foreach ($results as $result) {
            $comment = $this->getComment($result);
            $comments[] = $comment;
        }

        return $comments;
    }

    public function getCommentById(int $commentId): ?Comment
    {
        $query = $this->db->prepare('SELECT * FROM comments WHERE id = :id');
        $query->execute(['id' => $commentId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return $this->getComment($result);
    }

    public function updateComment(Comment $comment): bool
    {
        $db = Db::getInstance();

        $sql = "UPDATE comments SET 
                    content = :content,
                    author_id = :author_id,
                    created_at = :created_at,
                    parent_id = :parent_id,
                    post_id = :post_id,
                    is_approved = :is_approved
                WHERE id = :id";

        $stmt = $this->getCommentByPDO($db, $sql, $comment);
        $stmt->bindValue(':id', $comment->getId());

        return $stmt->execute();
    }

    public function deleteComment(int $commentId): bool
    {
        $db = Db::getInstance();

        $sql = "DELETE FROM comments WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $commentId);

        return $stmt->execute();
    }

    private function getComment(array $result): Comment
    {
        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $result['created_at']);

        return new Comment(
            $result['id'],
            $result['content'],
            $result['author_id'],
            $createdAt,
            $result['parent_id'],
            $result['post_id'],
            $result['is_approved']
        );
    }

    /**
     * @param PDO|null $db
     * @param string $sql
     * @param Comment $comment
     * @return false|\PDOStatement
     */
    public function getCommentByPDO(?PDO $db, string $sql, Comment $comment): \PDOStatement|false
    {
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':content', $comment->getContent());
        $stmt->bindValue(':author_id', $comment->getAuthorId());
        $stmt->bindValue(':created_at', $comment->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':parent_id', $comment->getParentId());
        $stmt->bindValue(':post_id', $comment->getPostId());
        $stmt->bindValue(':is_approved', $comment->isApproved());
        return $stmt;
    }

    public function setChildren(?int $id): array|null
    {
        $query = $this->db->prepare('SELECT * FROM comments WHERE parent_id = :parent_id AND is_approved = 1');
        $query->execute(['parent_id' => $id]);
        $result = $query->fetchAll();
        if (empty($result)) {
            return null;
        }
        $array = [];
        /** @var Comment $comment */
        foreach ($result as $comment){
            $array[] = $comment['id'];
        }

        return $array;
    }
}
