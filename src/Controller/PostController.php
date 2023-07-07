<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
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
        $post = $this->postRepository->findBySlug($slug);
        if (!$post) {
            return new Response('Post not found', Response::HTTP_NOT_FOUND);
        }
        $content = $this->twig->render('app/post/show.html.twig', ['post' => $post]);
        return new Response($content);
    }
}
