<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;

session_start(); // Démarrer la session

class UserService
{
    public static function isAdmin(): bool
    {
        if (isset($_SESSION['user']['id'])) {
            $userRepository = new UserRepository();
            $user = $userRepository->getUserById($_SESSION['user']['id']);
            return $user->getRole() === User::ROLE_ADMIN;
        }

        return false;
    }
}