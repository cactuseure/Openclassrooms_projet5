<?php

namespace App\Entity;

use DateTimeImmutable;

class User
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    private ?int $id;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $email;
    private ?string $username;
    private ?string $password;
    private ?DateTimeImmutable $created_at;
    private ?string $role;
    private ?string $reset_token;
    private ?bool $is_active;

    public function __construct(
        int               $id = null,
        string            $firstName = null,
        string            $lastName = null,
        string            $email = null,
        string            $username = null,
        string            $password = null,
        string            $reset_token = null,
        string            $role = null,
        DateTimeImmutable $created_at = null,
        bool              $is_active = null,
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->reset_token = $reset_token;
        $this->created_at = $created_at;
        $this->is_active = $is_active;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return User
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getLabelRole(): string
    {
        return match ($this->role) {
            'ROLE_ADMIN' => 'administrateur',
            'ROLE_USER' => 'utilisateur',
            default => 'inconnu',
        };
    }

    /**
     * @param string $role
     * @return User
     */
    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    /**
     * @param string|null $reset_token
     * @return User
     */
    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;
        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $created_at
     * @return User
     */
    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @param bool $isActive
     * @return User
     */
    public function setActive(bool $isActive): self
    {
        $this->is_active = $isActive;
        return $this;
    }


    /**
     * @param array $data
     * @return User|null
     */
    public static function createFromDatabase(array $data): ?User
    {
        if (!$data) {
            return null;
        }
        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at']);
        return new self(
            $data['id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['username'],
            $data['password'],
            $data['reset_token'],
            $data['role'],
            $createdAt,
            $data['is_active'],
        );
    }
}