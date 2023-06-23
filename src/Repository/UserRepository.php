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
            $stmt = $this->db->prepare('INSERT INTO user (first_name, last_name, email, username, password, reset_token, profile_image , role, created_at) VALUES (:first_name, :last_name, :email, :username, :password, :reset_token, :profile_image, :role, :created_at)');
            $stmt->bindValue(':first_name', $user->getFirstName());
            $stmt->bindValue(':last_name', $user->getLastName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':username', $user->getUsername());
            $stmt->bindValue(':password', $user->getPassword());
            $stmt->bindValue(':reset_token', $user->getResetToken());
            $stmt->bindValue(':profile_image', $user->getProfileImage());
            $stmt->bindValue(':role', $user->getRole());
            $stmt->bindValue(':created_at', $user->getCreatedAt()->format('Y-m-d H:i:s'));

            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \PDOException("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `email`=:email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result !== null) {
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
                $result['profile_image'],
                $result['id'],
            );
        }

        // Aucun utilisateur correspondant trouvé
        return null;
    }

    public function getUserById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `id`=:id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result !== null) {

            return new User(
                $result['first_name'],
                $result['last_name'],
                $result['email'],
                $result['password'],
                $result['id'],
                $result['role'],
            );

        }

        // Aucun utilisateur correspondant trouvé
        return null;
    }

    public function saveResetToken(int $userId, string $resetToken): void
    {
        $stmt = $this->db->prepare("UPDATE `user` SET `reset_token`=:resetToken WHERE `id`=:userId");
        $stmt->bindValue(':resetToken', $resetToken);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
    }

    public function isEmailTaken(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `user` WHERE `email` = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return ($count > 0);
    }
    public function isUsernameTaken(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `user` WHERE `username` = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return ($count > 0);
    }
    public function updateUserProfileImage(int $userId, string $filename): bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE user SET profile_image = :filename WHERE id = :userId');
            $stmt->bindValue(':filename', $filename);
            $stmt->bindValue(':userId', $userId);

            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \PDOException("Erreur lors de la mise à jour de l'image de profil de l'utilisateur : " . $e->getMessage());
        }
    }

}
