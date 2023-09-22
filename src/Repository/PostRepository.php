<?php

namespace App\Repository;

use App\Core\Db;
use App\Entity\Post;
use PDO;

/**
 * Classe de gestion des articles en base de données.
 */
class PostRepository
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * Récupère tous les articles actifs de la base de données.
     *
     * @return array Un tableau d'objets Post.
     */
    public function getPosts(): array
    {
        $query = $this->db->prepare('SELECT * FROM post WHERE is_active = :is_active');
        $query->execute(['is_active' => 1]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $posts = [];
        foreach ($results as $data) {
            $post = Post::createFromDatabase($data);
            $posts[] = $post;
        }
        return $posts;
    }

    /**
     * Récupère tous les articles de la base de données.
     *
     * @return array Un tableau d'objets Post.
     */
    public function getAllPosts(): array
    {
        $query = $this->db->query('SELECT * FROM post');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $posts = [];
        foreach ($results as $data) {
            $post = Post::createFromDatabase($data);
            $posts[] = $post;
        }
        return $posts;
    }

    /**
     * Recherche un article par son slug.
     *
     * @param string $slug Le slug de l'article à rechercher.
     * @return Post|null L'objet Post correspondant ou null si non trouvé.
     */
    public function findBySlug(string $slug): ?Post
    {
        $query = $this->db->prepare('SELECT * FROM post WHERE slug = :slug');
        $query->execute(['slug' => $slug]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return Post::createFromDatabase($result);
    }

    /**
     * Recherche un article par son ID.
     *
     * @param int $id L'ID de l'article à rechercher.
     * @return Post|null L'objet Post correspondant ou null si non trouvé.
     */
    public function getPostById(int $id): ?Post
    {
        $query = $this->db->prepare('SELECT * FROM post WHERE id = :id');
        $query->execute(['id' => $id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return Post::createFromDatabase($result);
    }

    /**
     * Crée un nouvel article dans la base de données.
     *
     * @param Post $post L'article à créer.
     */
    public function createPost(Post $post): void
    {
        $db = Db::getInstance();

        $sql = "INSERT INTO post (title, slug, thumbnail, hat, content, created_at, updated_at, is_active, user_id)
            VALUES (:title, :slug, :thumbnail, :hat, :content, :created_at, :updated_at, :is_active, :user_id)";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':title', $post->getTitle());
        $stmt->bindValue(':slug', $post->getSlug());
        $stmt->bindValue(':thumbnail', $post->getThumbnail());
        $stmt->bindValue(':hat', $post->getHat());
        $stmt->bindValue(':content', $post->getContent());
        $stmt->bindValue(':created_at', $post->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':updated_at', $post->getUpdatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':is_active', $post->isActive());
        $stmt->bindValue(':user_id', $post->getUserId());
        $stmt->execute();
    }

    /**
     * Met à jour un article dans la base de données.
     *
     * @param Post $post L'article à mettre à jour.
     * @return bool true si la mise à jour a réussi, sinon false.
     */
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
                    user_id = :user_id
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title', $post->getTitle());
        $stmt->bindValue(':slug', $post->getSlug());
        $stmt->bindValue(':thumbnail', $post->getThumbnail());
        $stmt->bindValue(':hat', $post->getHat());
        $stmt->bindValue(':content', $post->getContent());
        $stmt->bindValue(':updated_at', $post->getUpdatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':is_active', $post->isActive());
        $stmt->bindValue(':user_id', $post->getUserId());
        $stmt->bindValue(':id', $post->getId());
        return $stmt->execute();
    }

    /**
     * Vérifie si un slug est déjà utilisé pour un autre article.
     *
     * @param string $slug Le slug à vérifier.
     * @param bool $exceptHimself Indique si l'article actuel doit être exclu de la vérification.
     * @return bool true si le slug est déjà utilisé, sinon false.
     */
    public function isSlugIsTaken(string $slug, bool $exceptHimself = false): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM `post` WHERE `slug` = :slug");
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($post) {
            if ($exceptHimself && $post['slug'] === $slug) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Supprime un article de la base de données.
     *
     * @param int $postId L'ID de l'article à supprimer.
     * @return bool true si la suppression a réussi, sinon false.
     */
    public function deletePost(int $postId): bool
    {
        $db = Db::getInstance();
        $sql = "DELETE FROM post WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $postId);

        return $stmt->execute();
    }

    /**
     * Génère un slug à partir d'un titre d'article.
     *
     * @param string $title Le titre de l'article.
     * @return string Le slug généré.
     */
    function generateSlug(string $title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        return trim($slug, '-');
    }

    /**
     * Inverse le statut d'activation/désactivation d'un article.
     *
     * @param int $postId L'ID de l'article à activer/désactiver.
     * @return bool true si le changement de statut a réussi, sinon false.
     */
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