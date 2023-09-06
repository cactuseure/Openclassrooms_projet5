<?php

namespace App\Entity;

use DateTimeImmutable;

class ContactMessage
{
    private string $name;
    private string $lastname;
    private string $email;
    private string $message;
    private DateTimeImmutable $createdAt;

    public function __construct(string $name, string $lastname, string $email, string $message, DateTimeImmutable $createdAt)
    {
        $this->name = $name;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->message = $message;
        $this->createdAt = $createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
