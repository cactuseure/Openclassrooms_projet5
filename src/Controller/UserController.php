<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends AbstractController
{
    protected SessionInterface $session;
    public function __construct(SessionInterface $session)
    {
        parent::__construct();
        $this->session = $session;
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function register(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $firstName = '';
        $lastName = '';
        $email = '';
        $username = '';

        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('first_name') ?? '';
            $lastName = $request->request->get('last_name') ?? '';
            $email = $request->request->get('email') ?? '';
            $username = $request->request->get('username') ?? '';
            $password = $request->request->get('password') ?? '';
            $confirmPassword = $request->request->get('confirm_password') ?? '';

            if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
                $errorMessage = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($password !== $confirmPassword) {
                $errorMessage = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
                $errorMessage = 'Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et une minuscule.';
            } else {
                $userRepository = new UserRepository();

                $existingUserMail = $userRepository->isEmailTaken($email);
                $existingUserName = $userRepository->isUsernameTaken($username);
                if ($existingUserMail) {
                    $errorMessage = 'Un compte avec cet e-mail existe déjà.';
                } elseif ($existingUserName) {
                    $errorMessage = 'Un compte avec cet username existe déjà.';
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
                        false
                    );
                    $userRepository->createUser($user);
                    $successMessage = 'Inscription enregistré, en attende de validation';
                }
            }
        }

        return $this->render('/app/user/registration.html.twig',
            [
                'message_success' => $successMessage,
                'message_error' => $errorMessage,
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
    public function login(Request $request): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {
            $userRepository = new UserRepository();
            $email = $request->request->get('email') ?? '';
            $password = $request->request->get('password') ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs du formulaire.';
            } else {
                $user = $userRepository->getUserByEmail($email);
                if ($user && password_verify($password, $user->getPassword())) {
                    if ($user->isActive()){
                        $userData = [
                            'id' => $user->getId(),
                            'firstName' => $user->getFirstName(),
                            'lastName' => $user->getLastName(),
                            'email' => $user->getEmail(),
                            'username' => $user->getUsername(),
                            'role' => $user->getLabelRole(),
                            'created_at' => $user->getCreatedAt(),
                            'is_connected' => true,
                        ];

                        // Utilisez la session Symfony pour stocker les données de l'utilisateur
                        $this->session->set('user', $userData);

                        return $this->redirectToRoute('mon-compte');
                    } else {
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
    public function account(): Response
    {
        return $this->render('/app/user/mon-compte.html.twig',);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function editProfile(Request $request): Response
    {

        $error = null;
        $userData = $this->session->get('user');

        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('firstName') ?? '';
            $lastName = $request->request->get('lastName') ?? '';
            $email = $request->request->get('email') ?? '';
            $username = $request->request->get('username') ?? '';

            // Vérifie si tous les champs du formulaire sont remplis
            if (empty($firstName) || empty($lastName) || empty($email) || empty($username)) {
                $error = 'Veuillez remplir tous les champs du formulaire.';
            } else {
                // Vérifie si l'utilisateur existe déjà dans la bdd
                $userRepository = new UserRepository();

                $existingUserMail = $userRepository->isEmailTaken($email, true,$userData['email']);
                $existingUserName = $userRepository->isUsernameTaken($username, true,$userData['username']);

                if ($existingUserMail) {
                    $error = 'Un compte avec cet e-mail existe déjà.';
                } elseif ($existingUserName) {
                    $error = 'Un compte avec cet username existe déjà.';
                } else {
                    $user = $userRepository->getUserById($userData['id']);

                    $newUser = new User($firstName, $lastName, $email, $username,$user->getPassword(),$user->getCreatedAt(),$user->getResetToken(),$user->getRole(),$user->isActive(),$user->getId());
                    try {
                        $userRepository->updateUser($newUser);
                    } catch (\PDOException $e) {
                        throw new \PDOException("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
                    }
                    $this->session->set('user', [
                        'id' => $newUser->getId(),
                        'firstName' => $newUser->getFirstName(),
                        'lastName' => $newUser->getLastName(),
                        'email' => $newUser->getEmail(),
                        'username' => $newUser->getUsername(),
                        'role' => $newUser->getLabelRole(),
                        'created_at' => $newUser->getCreatedAt(),
                        'is_connected' => true,
                    ]);
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
    public function editPassword(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $userData = $this->session->get('user');

        if ($request->isMethod('POST') && $userData['is_connected']) {
            $last_password = $request->request->get('last_password');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirmPassword');
            $userRepository = new UserRepository();
            $email = $userData['email'];

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
        $this->session->remove('user');

        return $this->redirectToRoute('connexion');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function forgetPassword(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email') ?? '';

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
    public function resetPassword(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password') ?? '';
            $confirmPassword = $request->request->get('password_confirm') ?? '';

            if (empty($password) || empty($confirmPassword)) {
                $errorMessage = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($password !== $confirmPassword) {
                $errorMessage = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
                $errorMessage = 'Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et une minuscule.';
            } elseif (!$request->request->has('token')) {
                $errorMessage = 'Token expiré';
            } else {
                $token = $request->request->get('token');
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