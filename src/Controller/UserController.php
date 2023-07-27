<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function register(): Response
    {
        $error = null;
        $firstName = '';
        $lastName = '';
        $email = '';
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
                $error = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
                $error = 'Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et une minuscule.';
            } else {
                $userRepository = new UserRepository();

                $existingUserMail = $userRepository->isEmailTaken($email);
                $existingUserName = $userRepository->isUsernameTaken($username);
                if ($existingUserMail) {
                    $error = 'Un compte avec cet e-mail existe déjà.';
                } elseif ($existingUserName) {
                    $error = 'Un compte avec cet username existe déjà.';
                } else {
                    $user = new User(
                        $firstName,
                        $lastName,
                        $email,
                        $username,
                        password_hash($password, PASSWORD_DEFAULT),
                        new DateTimeImmutable(),
                        null,
                        'ROLE_USER',
                        null,
                    false
                    );

                    $userRepository->createUser($user);

                    return $this->redirectToRoute('');
                }
            }
        }

        return $this->render('/app/user/registration.html.twig',
            [
                'error' => $error,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'username' => $username,
            ]
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function login(): Response
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userRepository = new UserRepository();
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs du formulaire.';
            } else {
                $user = $userRepository->getUserByEmail($email);
                if ($user && password_verify($password, $user->getPassword())) {
                    if ($user->isActive()){
                        $_SESSION['user'] = [
                            'id' => $user->getId(),
                            'firstName' => $user->getFirstName(),
                            'lastName' => $user->getLastName(),
                            'email' => $user->getEmail(),
                            'username' => $user->getUsername(),
                            'role' => $user->getLabelRole(),
                            'created_at' => $user->getCreatedAt(),
                            'profile_image' => $user->getProfileImage(),
                            'is_connected' => true,
                        ];
                        return $this->redirectToRoute('mon-compte');
                    }else{
                        $error = 'Compte pas encore validé';
                    }
                } else {
                    $error = 'Identifiants invalides';
                }
            }
        }

        return $this->render('/app/user/login.html.twig', ['error' => $error]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function account(Request $request): Response
    {
        $error = null;
        return $this->render('/app/user/mon-compte.html.twig',
            [
                'error' => $error,
                'get' => $_GET,
            ]
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function editProfile(): Response
    {

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = $_POST['firstName'] ?? '';
            $lastName = $_POST['lastName'] ?? '';
            $email = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? '';

            // Vérifie si tous les champs du formulaire sont remplis
            if (empty($firstName) || empty($lastName) || empty($email) || empty($username)) {
                $error = 'Veuillez remplir tous les champs du formulaire.';
            } else {
                // Vérifie si l'utilisateur existe déjà dans la bdd
                $userRepository = new UserRepository();

                $existingUserMail = $userRepository->isEmailTaken($email, true);
                $existingUserName = $userRepository->isUsernameTaken($username, true);

                if ($existingUserMail) {
                    $error = 'Un compte avec cet e-mail existe déjà.';
                } elseif ($existingUserName) {
                    $error = 'Un compte avec cet username existe déjà.';
                } else {
                    $user = new User($firstName, $lastName, $email, $username);
                    try {
                        $userRepository->updateUser($user);
                    } catch (\PDOException $e) {
                        throw new \PDOException("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
                    }
                    return $this->redirectToRoute('mon-compte',
                        [
                            'message-edit' => 'success'
                        ]
                    );
                }
            }
        }
        return $this->render('/app/user/edit-profil.html.twig',
            [
                'error' => $error,
            ]
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function editPassword(): Response
    {
        $successMessage = null;
        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user']['is_connected']) {
            $last_password = $_POST['last_password'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];
            $userRepository = new UserRepository();
            $email = $_SESSION['user']['email'];

            if (empty($last_password) || empty($password) || empty($confirmPassword)) {
                $errorMessage = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($password !== $confirmPassword) {
                $errorMessage = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
                $errorMessage = 'Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et une minuscule.';
            } else {
                $user = $userRepository->getUserByEmail($email);
                if ($user && password_verify($last_password, $user->getPassword())) {
                    $userRepository->changePasswordBy($user->getId(), password_hash($password, PASSWORD_DEFAULT));
                    $successMessage = 'Mot de passe modifié avec succès';
                    return $this->redirectToRoute('mon-compte', [
                        'message_success' => $successMessage,
                        'message_error' => $errorMessage
                    ]);
                }
                $errorMessage = 'L\'ancien mot de passe est incorrect';
            }
        }
        return $this->render('/app/user/edit-password.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);

    }

    public function logout(): Response
    {
        unset($_SESSION['user']);
        return $this->redirectToRoute('connexion');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function forgetPassword(): Response
    {
        $successMessage = null;
        $errorMessage = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';

            $userRepository = new UserRepository();
            $user = $userRepository->getUserByEmail($email);

            if ($user !== null) {
                $resetToken = bin2hex(random_bytes(32));

                $userRepository->saveResetToken($user->getId(), $resetToken);

                $resetUrl = 'https://projet5.matteo-groult.com/reset-password?token=' . $resetToken;
                $subject = 'Réinitialisation de mot de passe';
                $body = "Bonjour " . $user->getFirstName() . ",\n\n"
                    . "Vous avez demandé une réinitialisation de mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :\n\n"
                    . $resetUrl . "\n\n"
                    . "Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.\n\n"
                    . "Cordialement,\n";

                $headers = 'From: noreply@projet5.matteo-groult.com' . "\r\n";
                mail($user->getEmail(), $subject, $body, $headers);
                $successMessage = 'Regardez votre boite mail';
            } else {
                $errorMessage = 'Aucun compte trouvé';
            }
        }

        return $this->render('/app/user/forget-password.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function resetPassword(): Response
    {
        $successMessage = null;
        $errorMessage = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['password_confirm'] ?? '';

            if (empty($password) || empty($confirmPassword)) {
                $errorMessage = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($password !== $confirmPassword) {
                $errorMessage = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
                $errorMessage = 'Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et une minuscule.';
            } elseif (!isset($_GET['token'])) {
                $errorMessage = 'Token expiré';
            } else {
                $token = $_GET['token'];
                $userRepository = new UserRepository();
                $user = $userRepository->getUserByResetToken($token);
                if ($user !== null) {
                    $userRepository->changePasswordBy($user->getId(), password_hash($password, PASSWORD_DEFAULT));
                    $userRepository->deleteToken($user->getId());
                    $successMessage = 'Mot de passe modifié avec succès';
                } else {
                    $errorMessage = 'Token invalide';
                }
            }

        }

        return $this->render('/app/user/reset-password.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }


}