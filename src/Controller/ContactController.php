<?php
// ContactController.php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Repository\ContactMessageRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ContactController extends AbstractController
{
    private ContactMessageRepository $contactMessageRepository;

    public function __construct(ContactMessageRepository $contactMessageRepository)
    {
        parent::__construct();
        $this->contactMessageRepository = $contactMessageRepository;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function contactForm(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $errors = null;

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $lastname = $request->request->get('lastname');
            $email = $request->request->get('email');
            $messageContent = $request->request->get('message');

            $errors = [];

            if (empty($name)) {
                $errors['name'] = 'Veuillez entrer votre nom.';
            }

            if (empty($lastname)) {
                $errors['lastname'] = 'Veuillez entrer votre prénom.';
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Veuillez entrer une adresse e-mail valide.';
            }

            if (empty($messageContent)) {
                $errors['message'] = 'Veuillez entrer votre message.';
            }

            if (count($errors) === 0) {
                $contactMessage = new ContactMessage(
                    $name,
                    $lastname,
                    $email,
                    $messageContent,
                    new DateTimeImmutable()
                );

                $recipientEmail = $_ENV['CONTACT_EMAIL']; // Remplacer 'CONTACT_EMAIL' par la clé réelle définie dans .env
                $subject = 'Nouveau message de contact';
                $headers = 'From: ' . $email;

                $mailSent = mail($recipientEmail, $subject, $messageContent, $headers);

                if ($mailSent) {
                    // Enregistrement en base de données à l'aide de la méthode saveContactMessage
                    $isSaved = $this->contactMessageRepository->saveContactMessage($contactMessage);

                    if ($isSaved) {
                        // Message de succès pour l'utilisateur
                        $successMessage = 'Votre message a été envoyé avec succès.';
                    } else {
                        // Message d'erreur si l'enregistrement en base de données a échoué
                        $errorMessage = 'Erreur lors de l\'enregistrement du message.';
                    }
                } else {
                    // Message d'erreur si l'envoi du mail a échoué
                    $errorMessage = 'Erreur lors de l\'envoi du message. Veuillez réessayer plus tard.';
                }
            } else {
                // Il y a des erreurs, afficher les messages d'erreur
                $errorMessage = 'Veuillez corriger les erreurs dans le formulaire.';
            }
        }


        return $this->render('app/home/contact.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
            'errors' => $errors, // Passer les erreurs à la vue pour les afficher
        ]);
    }
}
