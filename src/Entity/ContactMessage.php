<?php

namespace App\Entity;

use DateTimeImmutable;

/**
 *
 */
class ContactMessage
{

    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var string|null
     */
    private ?string $lastname;

    /**
     * @var string|null
     */
    private ?string $email;

    /**
     * @var string|null
     */
    private ?string $message;

    /**
     * @var DateTimeImmutable|null
     */
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
     * @param string|null $name
     * @return ContactMessage
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string|null $lastname
     * @return ContactMessage
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @param string|null $email
     * @return ContactMessage
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string|null $message
     * @return ContactMessage
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param DateTimeImmutable|null $createdAt
     * @return ContactMessage
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
