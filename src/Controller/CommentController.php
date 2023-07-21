<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CommentController extends AbstractController
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;

    public function __construct(CommentRepository $commentRepository, PostRepository $postRepository)
    {
        parent::__construct();
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function comment_post(Request $request): Response
    {
        $userRepository = new UserRepository();
        $commentRepository = new CommentRepository();
        $postId = $request->query->get('post_id');
        $commentId = $request->query->get('comment_id');
        $content = $request->request->get('comment_'.$commentId);
        $author = $this->getCurrentUser(); // Méthode pour obtenir l'auteur du commentaire connecté
        // Vérifier si le formulaire a été soumis et si le contenu du commentaire est non vide



        if ($request->isMethod('POST') && !empty($content)) {
            // Récupérer le post correspondant au slug
            $post = $this->postRepository->getPostById($postId);

            // Vérifier si le post existe
            if (!$post) {
                return new Response('Post not found', Response::HTTP_NOT_FOUND);
            }

            // Vérifier si le commentaire a un parent ou s'il est un nouveau commentaire
            if ($commentId === 'new') {
                $commentId = null; // Commentaire sans parent
            }

            // Créer un nouveau commentaire
            $comment = new Comment(
                null,
                $content,
                $author,
                new DateTimeImmutable(),
                $commentId,
                $post->getId(),
                false
            );

            if ($commentId == 0){
                $comment->setParentId(null);
            }

            // Enregistrer le commentaire en base de données
            $this->commentRepository->createComment($comment);

            // Rediriger vers la page du post avec un message de succès
            $successMessage = 'Commentaire enregistré (il sera visible après validation)';


            // Récupérer les commentaires mis à jour
            $comments = $this->commentRepository->getCommentsByPostId($post->getId());

            // Rendre le template avec les données
            $content = $this->twig->render('app/post/show.html.twig', [
                'post' => $post,
                'comments' => $comments,
                'message_success' => $successMessage,
                'message_error' => null,
                'userRepository' => $userRepository,
                'commentRepository' => $commentRepository
            ]);

            return new Response($content);
        }

        // Rediriger vers la page du post avec un message d'erreur
        $errorMessage = 'Error: Unable to post the comment.';


        // Récupérer le post correspondant au slug
        $post = $this->postRepository->getPostById($postId);

        // Vérifier si le post existe
        if (!$post) {
            return new Response('Post not found', Response::HTTP_NOT_FOUND);
        }

        // Récupérer les commentaires
        $comments = $this->commentRepository->getCommentsByPostId($post->getId());

        // Rendre le template avec les données
        $content = $this->twig->render('app/post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'message_success' => null,
            'message_error' => $errorMessage,
            'userRepository' => $userRepository,
            'commentRepository' => $commentRepository
        ]);

        return new Response($content);
    }




    public function getCurrentUser(): int
    {
        if (isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }
        return 0;
    }
}
