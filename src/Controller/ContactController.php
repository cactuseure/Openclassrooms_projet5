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

/**
 * Contrôleur pour la gestion du formulaire de contact.
 */
class ContactController extends AbstractController
{
    private ContactMessageRepository $contactMessageRepository;

    public function __construct(ContactMessageRepository $contactMessageRepository)
    {
        parent::__construct();
        $this->contactMessageRepository = $contactMessageRepository;
    }


    /**
     * Gère la soumission et le traitement du formulaire de contact.
     *
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

                $contactMessage = (new ContactMessage())
                ->setName($name)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setMessage($messageContent)
                ->setCreatedAt(new DateTimeImmutable());


                $recipientEmail = $_ENV['CONTACT_EMAIL'];
                $subject = 'Nouveau message de contact';
                $headers = 'From: ' . $email;

                $mailSent = mail($recipientEmail, $subject, $messageContent, $headers);

                if ($mailSent) {
                    $isSaved = $this->contactMessageRepository->saveContactMessage($contactMessage);

                    if ($isSaved) {
                        $successMessage = 'Votre message a été envoyé avec succès.';
                    } else {
                        $errorMessage = 'Erreur lors de l\'enregistrement du message.';
                    }
                } else {
                    $errorMessage = 'Erreur lors de l\'envoi du message. Veuillez réessayer plus tard.';
                }
            } else {
                $errorMessage = 'Veuillez corriger les erreurs dans le formulaire.';
            }
        }


        return $this->render('app/home/contact.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
            'errors' => $errors,
        ]);
    }
}
