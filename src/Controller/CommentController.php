<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Contrôleur pour la gestion des commentaires liés aux articles.
 */
class CommentController extends AbstractController
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;
    protected SessionInterface $session;

    public function __construct(
        CommentRepository $commentRepository,
        PostRepository    $postRepository,
        SessionInterface  $session
    )
    {
        parent::__construct();
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->session = $session;
    }

    /**
     * Gère la création et l'affichage de commentaires sur un article.
     *
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function comment_post(Request $request): Response
    {
        $userRepository = new UserRepository();
        $commentRepository = new CommentRepository();
        $postId = $request->query->get('post_id');
        $commentId = $request->query->get('comment_id');
        $content = $request->request->get('comment_' . $commentId);
        $author = $this->getCurrentUser();

        if ($request->isMethod('POST') && !empty($content)) {
            $post = $this->postRepository->getPostById($postId);

            if (!$post) {
                return new Response('Post not found', Response::HTTP_NOT_FOUND);
            }
            if ($commentId == 0) {
                $commentId = null;
            }

            $comment = (new Comment())
            ->setContent($content)
            ->setAuthorId($author)
            ->setCreatedAt(new DateTimeImmutable())
            ->setParentId($commentId)
            ->setPostId($post->getId())
            ->setApproved(false);

            $this->commentRepository->createComment($comment);
            $successMessage = 'Commentaire enregistré (il sera visible après validation)';
            $comments = $this->commentRepository->getCommentsByPostId($post->getId());
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
        $errorMessage = 'Error: Unable to post the comment.';
        $post = $this->postRepository->getPostById($postId);
        if (!$post) {
            return new Response('Post not found', Response::HTTP_NOT_FOUND);
        }
        $comments = $this->commentRepository->getCommentsByPostId($post->getId());
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

    /**
     * Récupère l'ID de l'utilisateur actuellement connecté.
     *
     * @return int
     */
    public function getCurrentUser(): int
    {
        $userData = $this->session->get('user');
        if (isset($userData['id'])) {
            return $userData['id'];
        }
        return 0;
    }
}
