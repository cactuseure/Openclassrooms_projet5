<?php

namespace App\Entity;

class User
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    private ?int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private string $role = self::ROLE_USER;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        ?int $id = null,
        ?string $role = null,
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

        // Vérifier la validité du mot de passe
        if (empty($password)) {
            throw new \InvalidArgumentException('Le mot de passe ne peut pas être vide.');
        }

        // Assigner les valeurs aux propriétés de l'objet User
        if ($id !== null) {
            $this->id = $id;
        }
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        if ($role !== null) {
            $this->role = $role;
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
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

// Getters et setters pour toutes les propriétés

}