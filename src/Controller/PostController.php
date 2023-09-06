<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): Response
    {
        return $this->render('app/post/posts.html.twig', ['posts' => $this->postRepository->getPosts()]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
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

        $comments = $this->commentRepository->getCommentsByPostId($post->getId());
        $content = $this->twig->render('app/post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
            'userRepository' => $userRepository,
            'commentRepository' => $commentRepository
        ]);

        return new Response($content);
    }
}
