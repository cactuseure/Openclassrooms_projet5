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

    public function findAll(): array
    {
        $query = $this->db->query('SELECT * FROM post');
        $results = $query->fetchAll();
        $format = 'Y-m-d H:i:s';
        $posts = [];

        foreach ($results as $result) {
            $post = $this->getPost($format, $result);
            $posts[] = $post;
        }

        return $posts;
    }


    public function create(Post $post): void
    {

    }

    public function findBySlug(string $slug): ?Post
    {
        $query = $this->db->prepare('SELECT * FROM post WHERE slug = :slug');
        $query->execute(['slug' => $slug]);
        $result = $query->fetch();

        if (!$result) {
            return null;
        }

        $format = 'Y-m-d H:i:s';
        return $this->getPost($format, $result);
    }

    /**
     * @param string $format
     * @param mixed $result
     * @return Post
     */
    public function getPost(string $format, mixed $result): Post
    {
        $createdAt = DateTimeImmutable::createFromFormat($format, $result['created_at']);
        $updatedAt = DateTimeImmutable::createFromFormat($format, $result['updated_at']);
        $post = new Post($result['title'], $result['slug'], $result['thumbnail'], $result['hat'], $result['content'], $createdAt, $updatedAt, $result['author_id'], $result['category_id']);
        return $post;
    }
}