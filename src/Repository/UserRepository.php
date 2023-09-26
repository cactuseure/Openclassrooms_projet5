<?php

namespace App\Repository;

use App\Entity\User;
use App\Core\Db;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Classe de gestion des utilisateurs en base de données.
 */
class UserRepository
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * Crée un nouvel utilisateur dans la base de données.
     *
     * @param User $user L'utilisateur à créer.
     * @return bool true si la création a réussi, sinon false.
     * @throws PDOException En cas d'erreur lors de la création.
     */
    public function createUser(User $user): bool
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO user (first_name, last_name, email, username, password, reset_token, role, created_at,is_active) VALUES (:first_name, :last_name, :email, :username, :password, :reset_token, :role, :created_at, :is_active)');
            $this->bindAllValue($stmt, $user);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    /**
     * Récupère un utilisateur par son adresse email.
     *
     * @param string $email L'adresse email de l'utilisateur à rechercher.
     * @return User|null L'objet User correspondant ou null si non trouvé.
     */
    public function getUserByEmail(string $email): ?User
    {
        $query = $this->db->prepare("SELECT * FROM user WHERE email = :email");
        $query->execute(['email' => $email]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return User::createFromDatabase($result);
    }

    /**
     * Récupère un utilisateur par son jeton de réinitialisation de mot de passe.
     *
     * @param string $resetToken Le jeton de réinitialisation à rechercher.
     * @return User|null L'objet User correspondant ou null si non trouvé.
     */
    public function getUserByResetToken(string $resetToken): ?User
    {
        $query = $this->db->prepare("SELECT * FROM user WHERE reset_token = :resetToken");
        $query->execute(['resetToken' => $resetToken]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return User::createFromDatabase($result);
    }

    /**
     * Supprime le jeton de réinitialisation d'un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur.
     * @return bool true si la suppression a réussi, sinon false.
     */
    public function deleteToken(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `reset_token` = NULL WHERE `id` = :userId");
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    /**
     * Récupère un utilisateur par son ID.
     *
     * @param int $id L'ID de l'utilisateur à rechercher.
     * @return User|null L'objet User correspondant ou null si non trouvé.
     */
    public function getUserById(int $id): ?User
    {
        $query = $this->db->prepare('SELECT * FROM user WHERE id = :id');
        $query->execute(['id' => $id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return User::createFromDatabase($result);
    }

    /**
     * Sauvegarde un jeton de réinitialisation pour un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur.
     * @param string $resetToken Le jeton de réinitialisation à sauvegarder.
     */
    public function saveResetToken(int $userId, string $resetToken): void
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `reset_token`=:resetToken WHERE `id`=:userId");
        $stmt->bindValue(':resetToken', $resetToken);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
    }

    /**
     * Vérifie si une adresse email est déjà utilisée par un autre utilisateur.
     *
     * @param string $email L'adresse email à vérifier.
     * @param bool $exceptHimself Indique si l'utilisateur actuel doit être exclu de la vérification.
     * @param string $sessionEmail L'adresse email de la session en cours.
     * @return bool true si l'adresse email est déjà utilisée, sinon false.
     */
    public function isEmailTaken(string $email, bool $exceptHimself = false, string $sessionEmail = ''): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `email` = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if ($exceptHimself && $user['email'] === $sessionEmail) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Vérifie si un nom d'utilisateur est déjà utilisé par un autre utilisateur.
     *
     * @param string $username Le nom d'utilisateur à vérifier.
     * @param bool $exceptHimself Indique si l'utilisateur actuel doit être exclu de la vérification.
     * @param string $sessionUsername Le nom d'utilisateur de la session en cours.
     * @return bool true si le nom d'utilisateur est déjà utilisé, sinon false.
     */
    public function isUsernameTaken(string $username, bool $exceptHimself = false, string $sessionUsername = ''): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `username` = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if ($exceptHimself && $user['username'] === $sessionUsername) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Met à jour les informations d'un utilisateur dans la base de données.
     *
     * @param User $user L'utilisateur à mettre à jour.
     * @return bool|null true si la mise à jour a réussi, sinon false.
     * @throws PDOException En cas d'erreur lors de la mise à jour.
     */
    public function updateUser(User $user): ?bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email, username = :username, password = :password, reset_token = :reset_token, role = :role, created_at = :created_at, is_active = :is_active WHERE id = :user_id');
            $this->bindAllValue($stmt, $user);
            $stmt->bindValue(':user_id', $user->getId());
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
        }
    }

    /**
     * Change le mot de passe d'un utilisateur.
     *
     * @param int|null $userId L'ID de l'utilisateur dont le mot de passe doit être changé.
     * @param string $newPassword Le nouveau mot de passe.
     * @return bool true si le changement de mot de passe a réussi, sinon false.
     */
    public function changePasswordBy(?int $userId, string $newPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `password` = :newPassword WHERE `id` = :userId");
        $stmt->bindValue(':newPassword', $newPassword);
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    /**
     * Récupère tous les utilisateurs de la base de données.
     *
     * @return array Un tableau d'objets User.
     */
    public function getUsers(): array
    {
        $query = $this->db->query('SELECT * FROM user');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($results as $data) {
            $user = User::createFromDatabase($data);
            $users[$user->getId()] = $user;
        }
        return $users;
    }

    /**
     * Inverse le statut d'activation/désactivation d'un utilisateur.
     *
     * @param User $user L'utilisateur dont le statut doit être inversé.
     * @return bool true si le changement de statut a réussi, sinon false.
     */
    public function swapActif(User $user): bool
    {
        $newStatus = $user->isActive() ? 0 : 1;

        $db = Db::getInstance();
        $sql = "UPDATE user SET is_active = :is_active WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':is_active', $newStatus);
        $stmt->bindValue(':id', $user->getId());

        return $stmt->execute();
    }

    /**
     * Inverse le rôle d'un utilisateur entre "ROLE_ADMIN" et "ROLE_USER".
     *
     * @param User $user L'utilisateur dont le rôle doit être inversé.
     * @return bool true si le changement de rôle a réussi, sinon false.
     */
    public function swapRole(User $user): bool
    {
        if ($user->getRole() == 'ROLE_ADMIN'){
            $newRole = 'ROLE_USER';
        }else{
            $newRole = 'ROLE_ADMIN';
        }
        $db = Db::getInstance();
        $sql = "UPDATE user SET role = :role WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':role', $newRole);
        $stmt->bindValue(':id', $user->getId());

        return $stmt->execute();
    }

    /**
     * Lie les valeurs d'un utilisateur à une instruction PDO.
     *
     * @param false|PDOStatement $stmt L'instruction PDO à laquelle lier les valeurs.
     * @param User $user L'utilisateur dont les valeurs doivent être liées.
     */
    public function bindAllValue(false|PDOStatement $stmt, User $user): void
    {
        $stmt->bindValue(':first_name', $user->getFirstName());
        $stmt->bindValue(':last_name', $user->getLastName());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':username', $user->getUsername());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':reset_token', $user->getResetToken());
        $stmt->bindValue(':role', $user->getRole());
        $stmt->bindValue(':is_active', $user->isActive());
        $stmt->bindValue(':created_at', $user->getCreatedAt()->format('Y-m-d H:i:s'));
    }
}
