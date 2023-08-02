<?php

namespace App\Repository;

use App\Entity\User;
use App\Core\Db;
use DateTimeImmutable;

class UserRepository
{
    private ?\PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function createUser(User $user): bool
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO user (first_name, last_name, email, username, password, reset_token, role, created_at,is_active) VALUES (:first_name, :last_name, :email, :username, :password, :reset_token, :role, :created_at, :is_active)');
            $this->bindAllValue($stmt, $user);
            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \PDOException("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `email`=:email");
        $stmt->bindValue(':email', $email);
        return $this->getUserByPdo($stmt);
    }

    public function getUserByResetToken(string $resetToken): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `reset_token` = :resetToken");
        $stmt->bindValue(':resetToken', $resetToken);
        return $this->getUserByPdo($stmt);
    }

    public function deleteToken(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `reset_token` = NULL WHERE `id` = :userId");
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    public function getUserById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `id`=:id");
        $stmt->bindValue(':id', $id);
        return $this->getUserByPdo($stmt);
    }

    public function saveResetToken(int $userId, string $resetToken): void
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `reset_token`=:resetToken WHERE `id`=:userId");
        $stmt->bindValue(':resetToken', $resetToken);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
    }

    public function isEmailTaken(string $email, bool $exceptHimself = false): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `email` = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            if ($exceptHimself && isset($_SESSION['user']['email']) && $user['email'] === $_SESSION['user']['email']) {
                return false;
            }
            return true;
        }
        return false;
    }
    public function isUsernameTaken(string $username, bool $exceptHimself = false): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `username` = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            if ($exceptHimself && isset($_SESSION['user']['username']) && $user['username'] === $_SESSION['user']['username']) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function updateUser(User $user): ?bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email, username = :username, password = :password, reset_token = :reset_token, role = :role, created_at = :created_at, is_active = :is_active WHERE id = :user_id');
            $this->bindAllValue($stmt, $user);
            $stmt->bindValue(':user_id', $user->getId());
            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \PDOException("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
        }
    }

    /**
     * @param false|\PDOStatement $stmt
     * @return User|null
     */
    public function getUserByPdo(false|\PDOStatement $stmt): ?User
    {
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $result['created_at']);
            // Vérifie si la conversion a réussi
            if ($createdAt === false) {
                throw new \InvalidArgumentException('La valeur de "created_at" n\'est pas un format de date et d\'heure valide.');
            }
            return new User(
                $result['first_name'],
                $result['last_name'],
                $result['email'],
                $result['username'],
                $result['password'],
                $createdAt,
                $result['reset_token'],
                $result['role'],
                $result['is_active'],
                $result['id'],
            );

        }

        // Aucun utilisateur trouvé
        return null;
    }

    /**
     * @param false|\PDOStatement $stmt
     * @param User $user
     * @return void
     */
    public function bindAllValue(false|\PDOStatement $stmt, User $user): void
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

    public function changePasswordBy(?int $userId, string $newPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `password` = :newPassword WHERE `id` = :userId");
        $stmt->bindValue(':newPassword', $newPassword);
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    public function getUsers(): array
    {
        $users = [];
        $query = $this->db->query('SELECT * FROM user');
        $results = $query->fetchAll();
        foreach ($results as $result) {
            if ($result){
                $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $result['created_at']);
                $user = new User(
                    $result['first_name'],
                    $result['last_name'],
                    $result['email'],
                    $result['username'],
                    $result['password'],
                    $createdAt,
                    $result['reset_token'],
                    $result['role'],
                    $result['is_active'],
                    $result['id'],

                );
                $users[] = $user;
            }
        }
        return $users;
    }

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

}
