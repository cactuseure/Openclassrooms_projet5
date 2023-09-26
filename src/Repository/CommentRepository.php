<?php
namespace App\Repository;

use App\Core\Db;
use App\Entity\Comment;
use PDO;
use PDOStatement;

/**
 * Classe de gestion des commentaires en base de données.
 */
class CommentRepository
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }


    /**
     * Crée un commentaire dans la base de données.
     *
     * @param Comment $comment Le commentaire à créer.
     */
    public function createComment(Comment $comment): void
    {
        $db = Db::getInstance();
        $sql = "INSERT INTO comments (content, user_id, created_at, parent_id, post_id, is_approved) 
                VALUES (:content, :user_id, :created_at, :parent_id, :post_id, :is_approved)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':content', $comment->getContent());
        $stmt->bindValue(':user_id', $comment->getAuthorId());
        $stmt->bindValue(':created_at', $comment->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':parent_id', $comment->getParentId());
        $stmt->bindValue(':post_id', $comment->getPostId());
        $stmt->bindValue(':is_approved', $comment->isApproved());
        $stmt->execute();
    }

    /**
     * Récupère tous les commentaires depuis la base de données.
     *
     * @return array Liste des commentaires.
     */
    public function getAllComments(): array
    {
        $query = $this->db->query('SELECT * FROM comments');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];
        foreach ($results as $data) {
            $comment = Comment::createFromDatabase($data);
            $comments[$comment->getId()] = $comment;
        }
        return $comments;
    }

    /**
     * Récupère les commentaires liés à un article spécifique depuis la base de données.
     *
     * @param int $postId L'identifiant de l'article.
     * @return array Liste des commentaires liés à l'article.
     */
    public function getCommentsByPostId(int $postId): array
    {
        $query = $this->db->prepare('SELECT * FROM comments WHERE post_id = :post_id AND is_approved = 1');
        $query->execute(['post_id' => $postId]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];
        foreach ($results as $data) {
            $comment = Comment::createFromDatabase($data);
            $comments[$comment->getId()] = $comment;
        }
        return $comments;
    }

    /**
     * Récupère un commentaire par son identifiant depuis la base de données.
     *
     * @param int $commentId L'identifiant du commentaire.
     * @return Comment|null Le commentaire ou null s'il n'existe pas.
     */
    public function getCommentById(int $commentId): ?Comment
    {
        $query = $this->db->prepare('SELECT * FROM comments WHERE id = :id');
        $query->execute(['id' => $commentId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return Comment::createFromDatabase($result);
    }

    /**
     * Met à jour un commentaire dans la base de données.
     *
     * @param Comment $comment Le commentaire à mettre à jour.
     * @return bool true si la mise à jour a réussi, sinon false.
     */
    public function updateComment(Comment $comment): bool
    {
        $db = Db::getInstance();
        $sql = "UPDATE comments SET content = :content, user_id = :user_id, 
                created_at = :created_at, parent_id = :parent_id, post_id = :post_id, 
                is_approved = :is_approved WHERE id = :id";
        $stmt = $this->getCommentByPDO($db, $sql, $comment);
        $stmt->bindValue(':id', $comment->getId());
        return $stmt->execute();
    }

    /**
     * Supprime un commentaire de la base de données.
     *
     * @param int $commentId L'identifiant du commentaire à supprimer.
     * @return bool true si la suppression a réussi, sinon false.
     */
    public function deleteComment(int $commentId): bool
    {
        $db = Db::getInstance();
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $commentId);
        return $stmt->execute();
    }

    /**
     * Récupère les identifiants des commentaires enfants d'un commentaire parent donné.
     *
     * @param int|null $id L'identifiant du commentaire parent.
     * @return array|null Liste des identifiants des commentaires enfants ou null s'il n'y en a pas.
     */
    public function setChildren(?int $id): array|null
    {
        $query = $this->db->prepare('SELECT * FROM comments WHERE parent_id = :parent_id AND is_approved = 1');
        $query->execute(['parent_id' => $id]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return [];
        }
        $array = [];
        foreach ($result as $comment) {
            $array[] = $comment['id'];
        }
        return $array;
    }

    /**
     * Obtient une instance de PDOStatement pour la mise à jour d'un commentaire.
     *
     * @param PDO|null $db L'instance de PDO.
     * @param string $sql La requête SQL.
     * @param Comment $comment Le commentaire à mettre à jour.
     * @return PDOStatement|false L'instance de PDOStatement ou false en cas d'erreur.
     */
    public function getCommentByPDO(?PDO $db, string $sql, Comment $comment): PDOStatement|false
    {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':content', $comment->getContent());
        $stmt->bindValue(':user_id', $comment->getAuthorId());
        $stmt->bindValue(':created_at', $comment->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':parent_id', $comment->getParentId());
        $stmt->bindValue(':post_id', $comment->getPostId());
        $stmt->bindValue(':is_approved', $comment->isApproved());
        return $stmt;
    }
}
