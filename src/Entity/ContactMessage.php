<?php

namespace App\Entity;

use DateTimeImmutable;

/**
 *
 */
class ContactMessage
{
    private ?string $name;
    private ?string $lastname;
    private ?string $email;
    private ?string $message;
    private ?DateTimeImmutable $createdAt;

    /**
     * @param string|null $name
     * @param string|null $lastname
     * @param string|null $email
     * @param string|null $message
     * @param DateTimeImmutable|null $createdAt
     */
    public function __construct(
        string            $name = null,
        string            $lastname = null,
        string            $email = null,
        string            $message = null,
        DateTimeImmutable $createdAt = null
    )
    {
        $this->name = $name;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->message = $message;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $lastname
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string|null $message
     * @return void
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param DateTimeImmutable|null $createdAt
     * @return void
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
