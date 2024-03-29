<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends AbstractController
{

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        parent::__construct();
        $this->session = $session;
    }

    /**
     * Liste tous les articles en tant qu'administrateur.
     *
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function listPosts(): Response
    {
        $this->redirectIfNotAdmin();

        $postRepository = new PostRepository();
        $posts = $postRepository->getAllPosts();

        return $this->render('/app/admin/list-posts.html.twig', [
            'posts' => $posts,
        ]);
    }


    /**
     * Éditer un article en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function editPost(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $successMessage = null;
        $errorMessage = null;
        $post = null;

        if ($request->query->has('post_id') === TRUE) {
            $postRepository = new PostRepository();
            $postId = $request->query->get('post_id');
            $post = $postRepository->getPostById($postId);
            if ($post !== null) {
                if ($request->isMethod('POST') === TRUE) {
                    $title = $request->request->get('title');
                    $slug = $postRepository->generateSlug($title);
                    $hat = $request->request->get('hat');
                    $thumbnail = $request->request->get('thumbnail');
                    $content = $request->request->get('content');
                    if (empty($title) || empty($hat) || empty($content) || empty($slug) || empty($thumbnail)) {
                        $errorMessage = 'veuillez remplir tous les champs';
                    } elseif ($postRepository->isSlugIsTaken($slug, true)) {
                        $errorMessage = 'Un article possède deja ce titre';
                    } else {
                        $post->setTitle($title);
                        $post->setSlug($slug);
                        $post->setHat($hat);
                        $post->setThumbnail($thumbnail);
                        $post->setContent($content);
                        $post->setUpdatedAt(new DateTimeImmutable());
                        $postRepository->updatePost($post);
                        $successMessage = 'Article modifié avec succès';
                    }
                }
            } else {
                $errorMessage = 'article introuvable';
            }
        } else {
            $errorMessage = 'article introuvable';
        }

        return $this->render('/app/admin/edit-post.html.twig', [
            'post' => $post,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }

    /**
     * Ajouter un nouvel article en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function addPost(Request $request): Response
    {
        $this->redirectIfNotAdmin();
        $successMessage = null;
        $errorMessage = null;
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title') ?? '';
            $thumbnail = $request->request->get('thumbnail') ?? '';
            $hat = $request->request->get('hat') ?? '';
            $content = $request->request->get('content') ?? '';
            $postRepository = new PostRepository();
            $slug = $postRepository->generateSlug($title);
            if (empty($title) || empty($hat) || empty($content) || empty($thumbnail)) {
                $errorMessage = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($postRepository->isSlugIsTaken($slug)) {
                $errorMessage = 'Ce titre d\'article est déjà utilisé';
            } else {
                $post = (new Post())
                ->setTitle($title)
                ->setSlug($slug)
                ->setThumbnail($thumbnail)
                ->setHat($hat)
                ->setContent($content)
                ->setUserId($this->session->get('user')['id'])
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable())
                ->setIsActive(true);
                $postRepository->createPost($post);
                $successMessage = 'Article ajouté avec succès';
            }
        }

        return $this->render('/app/admin/add-post.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }

    /**
     * Vérifie si un utilisateur est connecté en tant qu'administrateur.
     *
     * @return bool
     */
    private function isUserLoggedInAdmin(): bool
    {
        $userData = $this->session->get('user');

        return ($userData !== null && $userData['role'] === 'administrateur');
    }


    /**
     *  Ajouter un nouvel article en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function deletePost(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $successMessage = null;
        $errorMessage = null;
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();

        if ($request->query->has('post_id')) {
            $postId = $request->query->get('post_id');
            $post = $postRepository->getPostById($postId);
            if ($post) {

                $commentsToDelete = $commentRepository->getCommentsByPostId($postId);
                foreach ($commentsToDelete as $comment) {
                    $commentRepository->deleteComment($comment->getId());
                }

                if ($postRepository->deletePost($postId)) {
                    $successMessage = 'Article : "' . $post->getTitle() . '" a été supprimé avec succès.';
                } else {
                    $errorMessage = 'Erreur lors de la suppression de l\'article';
                }
            } else {
                $errorMessage = 'Article introuvable';
            }
        } else {
            $errorMessage = 'Article introuvable';
        }

        $posts = $postRepository->getAllPosts();

        return $this->render('/app/admin/list-posts.html.twig', [
            'posts' => $posts,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }


    /**
     * Inverse le statut d'un article en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function swapStatus(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $successMessage = null;
        $errorMessage = null;
        $postRepository = new PostRepository();
        if ($request->query->has('post_id')) {
            $post = $postRepository->getPostById($request->query->get('post_id'));
            if ($post) {
                if (!$postRepository->swapStatus($request->query->get('post_id'))) {
                    $errorMessage = 'Erreur lors du changement d\'état';
                }

            } else {
                $errorMessage = 'Article introuvable';
            }
        } else {
            $errorMessage = 'Article introuvable';
        }

        $posts = $postRepository->getAllPosts();

        return $this->render('/app/admin/list-posts.html.twig', [
            'posts' => $posts,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }

    /**
     * Liste tous les utilisateurs en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function listUsers(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $userRepository = new UserRepository();
        $users = $userRepository->getUsers();
        $successMessage = $this->getSuccessMessage($request);
        $errorMessage = $this->getErrorMessage($request);
        $content = $this->twig->render('app/admin/list-users.html.twig', [
            'users' => $users,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
        ]);

        return new Response($content);
    }

    /**
     * Inverse le rôle d'un utilisateur en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     *
     */
    public function swapUserRole(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $successMessage = null;
        $errorMessage = null;
        $userRepository = new UserRepository();
        if ($request->query->has('user_id')) {
            $user = $userRepository->getUserById($request->query->get('user_id'));
            if ($user) {
                if (!$userRepository->swapRole($user)) {
                    $errorMessage = 'Erreur lors du changement d\'état';
                }
            } else {
                $errorMessage = 'Article introuvable';
            }
        } else {
            $errorMessage = 'Article introuvable';
        }

        $users = $userRepository->getUsers();
        $content = $this->twig->render('app/admin/list-users.html.twig', [
            'users' => $users,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
        ]);

        return new Response($content);
    }

    /**
     * Inverse l'état d'activité d'un utilisateur en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function swapUserStatus(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $successMessage = null;
        $errorMessage = null;
        $userRepository = new UserRepository();
        if ($request->query->has('user_id')) {
            $user = $userRepository->getUserById($request->query->get('user_id'));
            if ($user) {
                if (!$userRepository->swapActif($user)) {
                    $errorMessage = 'Erreur lors du changement d\'état';
                }
            } else {
                $errorMessage = 'User introuvable';
            }
        } else {
            $errorMessage = 'User introuvable';
        }

        $users = $userRepository->getUsers();
        $content = $this->twig->render('app/admin/list-users.html.twig', [
            'users' => $users,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
        ]);

        return new Response($content);
    }

    /**
     * Récupère un message de succès à afficher.
     *
     * @param Request $request
     * @return string|null
     */
    public function getSuccessMessage(Request $request): string|null
    {
        if ($request->isMethod('GET') && $request->query->has('comment_id')) {

            return $request->query->get('comment_id');
        } else {

            return null;
        }
    }

    /**
     * Récupère un message d'erreur à afficher.
     *
     * @param Request $request
     * @return string|null
     */
    public function getErrorMessage(Request $request): string|null
    {
        if ($request->isMethod('GET') && $request->query->has('comment_id')) {

            return $request->query->get('comment_id');
        } else {

            return null;
        }
    }

    /**
     * Liste tous les commentaires en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function listComments(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $commentRepository = new CommentRepository();
        $successMessage = $this->getSuccessMessage($request);
        $errorMessage = $this->getErrorMessage($request);

        return $this->extracted($commentRepository, $successMessage, $errorMessage);
    }

    /**
     * Supprime un commentaire en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function removeComment(Request $request): Response
    {
        $this->redirectIfNotAdmin();

        $successMessage = null;
        $errorMessage = null;

        $commentRepository = new CommentRepository();

        if ($this->isUserLoggedInAdmin() && $request->isMethod('GET') && $request->query->has('comment_id')) {
            $comment_id = $request->query->get('comment_id');
            if ($commentRepository->deleteComment($comment_id)) {
                $successMessage = 'Commentaires supprimés avec succès';
            } else {
                $errorMessage = 'Une erreur est survenue';
            }
        }

        return $this->extracted($commentRepository, $successMessage, $errorMessage);
    }


    /**
     * Approuve ou désapprouve un commentaire en tant qu'administrateur.
     *
     * @param Request $request
     * @return Response
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function approveComment(Request $request): Response
    {
        $this->redirectIfNotAdmin();
        $successMessage = null;
        $errorMessage = null;

        $commentRepository = new CommentRepository();

        if ($this->isUserLoggedInAdmin() && $request->isMethod('GET') && $request->query->has('comment_id')) {
            $comment_id = $request->query->get('comment_id');
            $comment = $commentRepository->getCommentById($comment_id);
            $comment->setApproved(!$comment->getStatus());
            if ($commentRepository->updateComment($comment) && $comment->getStatus()) {
                $successMessage = 'Le commentaire est maintenant visible';
                return $this->redirectToRoute('/admin/commentaires', ['message_success' => $successMessage]);
            } elseif ($commentRepository->updateComment($comment) && !$comment->getStatus()) {
                $successMessage = 'Le commentaire est maintenant invisible';
                return $this->redirectToRoute('/admin/commentaires', ['message_success' => $successMessage]);
            } else {
                $errorMessage = 'Une erreur est survenue';
            }
        }

        return $this->extracted($commentRepository, $successMessage, $errorMessage);
    }


    /**
     * Redirige vers la page d'accueil si l'utilisateur n'est pas un administrateur.
     *
     * @return void
     */
    private function redirectIfNotAdmin(): void
    {
        if (!$this->isUserLoggedInAdmin()) {
            header('Location: https://projet5.matteo-groult.com/');
        }
    }

    /**
     * @param CommentRepository $commentRepository
     * @param null $successMessage
     * @param string|null $errorMessage
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function extracted(CommentRepository $commentRepository, ?string $successMessage, ?string $errorMessage): Response
    {
        $postRepository = new PostRepository();
        $userRepository = new UserRepository();
        $comments = array_reverse($commentRepository->getAllComments());
        $arrayAuthor = array();
        $arrayPost = array();
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $arrayAuthor[$comment->getId()] = $userRepository->getUserById($comment->getAuthorId())->getUsername();
            $arrayPost[$comment->getId()] = $postRepository->getPostById($comment->getPostId())->getSlug();
        }

        return $this->render('app/admin/list-comments.html.twig', [
            'comments' => $comments,
            'postRepository' => $postRepository,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
            'arrayAuthor' => $arrayAuthor,
            'arrayPost' => $arrayPost
        ]);
    }

}
