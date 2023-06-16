<?php

namespace App\Repository;

use App\Entity\User;
use App\Core\Db;

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
            $stmt = $this->db->prepare('INSERT INTO user (first_name, last_name, email, password, role) VALUES (:first_name, :last_name, :email, :password, :role)');
            $stmt->bindValue(':first_name', $user->getFirstName());
            $stmt->bindValue(':last_name', $user->getLastName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':password', $user->getPassword());
            $stmt->bindValue(':role', $user->getRole());

            return $stmt->execute();
        } catch (\PDOException $e) {
            // Exemple :
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

            return new User(
                $result['first_name'],
                $result['last_name'],
                $result['email'],
                $result['password'],
                $result['id'],
                $result['role'],
                $result['reset_token']
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


}
