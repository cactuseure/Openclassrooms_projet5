<?php

namespace App\Entity;

use DateTimeImmutable;

class User
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    private ?int $id = null;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $email = null;
    private ?string $username = null;
    private ?string $password = null;
    private ?DateTimeImmutable $createdAt = null;
    private ?string $role = null;
    private ?string $profile_image = null;
    private ?string $reset_token = null;

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $username = null,
        ?string $password = null,
        ?DateTimeImmutable $createdAt = null,
        ?string $reset_token = null,
        ?string $role = null,
        ?string $profile_image = null,
        ?int $id = null,
    )
    {
        // Vérifier la validité du prénom
        if (empty($firstName)) {
            throw new \InvalidArgumentException('Le prénom ne peut pas être vide.');
        }

        // Vérifier la validité du nom de famille
        if (empty($lastName)) {
            throw new \InvalidArgumentException('Le nom de famille ne peut pas être vide.');
        }

        // Vérifier la validité de l'adresse email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('L\'adresse email n\'est pas valide.');
        }

        if (empty($username)) {
            throw new \InvalidArgumentException('Le pseudo n\'est pas valide.');
        }

        // Assigner les valeurs aux propriétés de l'objet User
        if ($id !== null) {
            $this->id = $id;
        }
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->createdAt = $createdAt;
        if ($role !== null) {
            $this->role = $role;
        }
        if ($reset_token !== null) {
            $this->reset_token = $reset_token;
        }
        if ($profile_image !== null) {
            $this->profile_image = $profile_image;
        }
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
     * @return string
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

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
     */
    public function setResetToken(?string $reset_token): void
    {
        $this->reset_token = $reset_token;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string|null
     */
    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    /**
     * @param string $profile_image
     */
    public function setProfileImage(string $profile_image): void
    {
        $this->profile_image = $profile_image;
    }

}