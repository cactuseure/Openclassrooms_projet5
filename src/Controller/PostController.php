<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 *
 */
class PostController extends AbstractController
{
    private PostRepository $postRepository;
    private CommentRepository $commentRepository;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
    }


    /**
     * Affiche la liste des articles (posts) du site.
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): Response
    {
        return $this->render('app/post/posts.html.twig', ['posts' => $this->postRepository->getPosts()]);
    }


    /**
     * Affiche un article (post) en fonction de son slug.
     *
     * @param string $slug
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(string $slug): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $userRepository = new UserRepository();
        $commentRepository = new CommentRepository();

        $post = $this->postRepository->findBySlug($slug);
        if (!$post) {

            return new Response('Post not found', Response::HTTP_NOT_FOUND);
        }
        $users = $userRepository->getUsers();
        $comments = $this->commentRepository->getCommentsByPostId($post->getId());
        $content = $this->twig->render('app/post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'users' => $users,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
            'userRepository' => $userRepository,
            'commentRepository' => $commentRepository
        ]);

        return new Response($content);
    }
}
