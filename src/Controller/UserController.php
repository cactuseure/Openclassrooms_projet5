<?php

namespace App\Controller;

use App\Core\Db;
use App\Entity\User;
use App\Notification\NotificationManager;
use App\Repository\UserRepository;
use App\Services\UserService;
use DateTimeImmutable;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use PDO;
use Symfony\Component\HttpFoundation\Response;
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

            // Vérifie si tous les champs du formulaire sont remplis
            if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
                $error = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
                $error = 'Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et une minuscule.';
            } else {
                // Vérifie si l'utilisateur existe déjà dans la base de données
                $userRepository = new UserRepository();

                $existingUserMail = $userRepository->isEmailTaken($email);
                $existingUserName = $userRepository->isUsernameTaken($username);
                if ($existingUserMail) {
                    $error = 'Un compte avec cet e-mail existe déjà.';
                }elseif ($existingUserName){
                    $error = 'Un compte avec cet username existe déjà.';
                } else {
                    // Créer un nouvel utilisateur
                    $user = new User($firstName, $lastName, $email, $username, password_hash($password, PASSWORD_DEFAULT), new DateTimeImmutable(),null,'ROLE_USER',null);

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
            $email = $_POST['email'];
            $password = $_POST['password'];
            // Vérifier si l'utilisateur existe dans la base de données
            $user = $userRepository->getUserByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                // Connexion réussie
                // Stocker les informations de l'utilisateur dans la session
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

                // Rediriger l'utilisateur vers la page de son compte ou une autre page appropriée
                return $this->redirectToRoute('mon-compte'); // Remplacez 'user' par le nom de la route correspondant à la page du compte utilisateur
                // Assurez-vous de mettre à jour la redirection selon votre structure de routes
            } else {
                // Identifiants invalides
                // Afficher un message d'erreur à l'utilisateur
                $error = 'Identifiants invalides';
            }
        }

        return $this->render('/app/user/login.html.twig', ['error' => $error]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function account(): Response
    {
        $error = null;
        return $this->render('/app/user/mon-compte.html.twig', ['error' => $error ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function editProfile(): Response
    {
        // Afficher le formulaire de modification du profil avec les informations actuelles de l'utilisateur
        return $this->render('/app/user/edit-profil.html.twig');
    }

    public function logout(): Response
    {
        // Supprimer les informations de l'utilisateur de la session
        unset($_SESSION['user']);

        // Rediriger l'utilisateur vers la page d'accueil ou une autre page appropriée
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
        // Vérifiez si le formulaire de demande de réinitialisation de mot de passe a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';

            // Vérifiez si l'e-mail existe dans la base de données
            $userRepository = new UserRepository();
            $user = $userRepository->getUserByEmail($email);

            if ($user !== null) {
                // Générez un jeton de réinitialisation de mot de passe unique
                $resetToken = bin2hex(random_bytes(32));

                // Enregistrez le jeton de réinitialisation dans la base de données pour l'utilisateur
                $userRepository->saveResetToken($user->getId(), $resetToken);

                // Envoyez l'e-mail de réinitialisation de mot de passe
                $resetUrl = 'https://projet5.matteo-groult.com/reset-password?token=' . $resetToken;
                $subject = 'Réinitialisation de mot de passe';
                $message = "Bonjour " . $user->getFirstName() . ",\n\n"
                    . "Vous avez demandé une réinitialisation de mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :\n\n"
                    . $resetUrl . "\n\n"
                    . "Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.\n\n"
                    . "Cordialement,\n";


                // Utilisez une bibliothèque ou un service pour envoyer l'e-mail
                // Exemple avec la fonction mail() de PHP (requiert une configuration SMTP)
                $headers = 'From: noreply@projet5.matteo-groult.com' . "\r\n";
                mail($user->getEmail(), $subject, $message, $headers);

                // Redirigez l'utilisateur vers une page de confirmation ou affichez un message de succès
                return $this->redirectToRoute('password-reset-requested');
            }
        }

        // Affichez le formulaire de demande de réinitialisation de mot de passe
        return $this->render('/app/user/forget-password.html.twig');
    }


    #[NoReturn] public function passwordResetRequested(): void
    {
        // Enregistrement de la notification dans la session
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Votre demande de réinitialisation de mot de passe a été soumise avec succès.',
            'exist' => true
        ];

        // Redirection vers la page d'accueil avec la notification
        header('Location: /');
        exit;
    }


}