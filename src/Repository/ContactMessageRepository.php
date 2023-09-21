<?php

namespace App\Repository;

use App\Core\Db;
use App\Entity\ContactMessage;
use PDO;

/**
 * Classe de gestion des messages de contact en base de données.
 */
class ContactMessageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * Enregistre un message de contact dans la base de données.
     *
     * @param ContactMessage $contactMessage Le message de contact à enregistrer.
     * @return bool true si l'enregistrement a réussi, sinon false.
     */
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
