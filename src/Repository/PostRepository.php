<?php

namespace App\Repository;

use App\Core\Db;
use App\Entity\Post;
use DateTimeImmutable;

class PostRepository
{
    private ?\PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getPosts(): array
    {
        $query = $this->db->query('SELECT * FROM post WHERE is_active = 1');
        $results = $query->fetchAll();
        $posts = [];
        foreach ($results as $result) {
            $post = $this->getPost($result);
            $posts[] = $post;
        }

        return $posts;
    }

    public function getAllPosts(): array
    {
        $query = $this->db->query('SELECT * FROM post');
        $results = $query->fetchAll();
        $posts = [];
        foreach ($results as $result) {
            $post = $this->getPost($result);
            $posts[] = $post;
        }

        return $posts;
    }

    public function getPostById(int $id): ?Post
    {
        $query = $this->db->prepare('SELECT * FROM post WHERE id = :id');
        $query->execute(['id' => $id]);
        $result = $query->fetch();
        if (!$result) {
            return null;
        }
        return $this->getPost($result);
    }

    public function createPost(Post $post): void
    {
        $db = Db::getInstance();

        $sql = "INSERT INTO post (title, slug, thumbnail, hat, content, created_at, updated_at, is_active, author_id)
            VALUES (:title, :slug, :thumbnail, :hat, :content, :created_at, :updated_at, :is_active, :author_id)";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':title', $post->getTitle());
        $stmt->bindValue(':slug', $post->getSlug());
        $stmt->bindValue(':thumbnail', $post->getThumbnail());
        $stmt->bindValue(':hat', $post->getHat());
        $stmt->bindValue(':content', $post->getContent());
        $stmt->bindValue(':created_at', $post->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':updated_at', $post->getUpdatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':is_active', $post->isActive());
        $stmt->bindValue(':author_id', $post->getAuthorId());

        $stmt->execute();
    }
    public function updatePost(Post $post): bool
    {
        $db = Db::getInstance();

        $sql = "UPDATE post SET 
                    title = :title,
                    slug = :slug,
                    thumbnail = :thumbnail,
                    hat = :hat,
                    content = :content,
                    updated_at = :updated_at,
                    is_active = :is_active,
                    author_id = :author_id
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title', $post->getTitle());
        $stmt->bindValue(':slug', $post->getSlug());
        $stmt->bindValue(':thumbnail', $post->getThumbnail());
        $stmt->bindValue(':hat', $post->getHat());
        $stmt->bindValue(':content', $post->getContent());
        $stmt->bindValue(':updated_at', $post->getUpdatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':is_active', $post->isActive());
        $stmt->bindValue(':author_id', $post->getAuthorId());
        $stmt->bindValue(':id', $post->getId());

        return $stmt->execute();
    }

    public function isSlugIsTaken(string $slug, bool $exceptHimself = false): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM `post` WHERE `slug` = :slug");
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $post = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($post) {
            if ($exceptHimself && $post['slug'] === $slug) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function deletePost(int $postId): bool
    {
        $db = Db::getInstance();

        $sql = "DELETE FROM post WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $postId);

        return $stmt->execute();
    }

    public function findBySlug(string $slug): ?Post
    {
        $query = $this->db->prepare('SELECT * FROM post WHERE slug = :slug');
        $query->execute(['slug' => $slug]);
        $result = $query->fetch();
        if (!$result) {
            return null;
        }
        return $this->getPost($result);
    }

    /**
     * @param mixed $result
     * @return Post
     */
    public function getPost( mixed $result): Post
    {
        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $result['created_at']);
        $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $result['updated_at']);
        return new Post($result['title'], $result['slug'], $result['thumbnail'], $result['hat'], $result['content'], $createdAt, $updatedAt,$result['is_active'], $result['author_id'], $result['id']);
    }

    function generateSlug(string $title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    public function swapStatus(int $postId): bool
    {
        $post = $this->getPostById($postId);
        if (!$post) {
            return false;
        }

        $newStatus = $post->isActive() ? 0 : 1;

        $db = Db::getInstance();
        $sql = "UPDATE post SET is_active = :is_active WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':is_active', $newStatus);
        $stmt->bindValue(':id', $postId);

        return $stmt->execute();
    }
}