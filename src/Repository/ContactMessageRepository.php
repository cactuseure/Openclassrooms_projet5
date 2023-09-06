<?php
// ContactMessageRepository.php

namespace App\Repository;

use App\Core\Db;
use App\Entity\ContactMessage;
use PDO;

class ContactMessageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function saveContactMessage(ContactMessage $contactMessage): bool
    {
        $sql = "INSERT INTO contact_messages (name, lastname, email, message, created_at)
            VALUES (:name, :lastname, :email, :message, :created_at)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':name', $contactMessage->getName());
        $stmt->bindValue(':lastname', $contactMessage->getLastname());
        $stmt->bindValue(':email', $contactMessage->getEmail());
        $stmt->bindValue(':message', $contactMessage->getMessage());
        $stmt->bindValue(':created_at', $contactMessage->getCreatedAt()->format('Y-m-d H:i:s'));

        return $stmt->execute();
    }
}
