<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): Response
    {
        return $this->render('app/home/index.html.twig');
    }

    /**
     * @param string $slug
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(string $slug): Response
    {
        $post = $this->postRepository->findBySlug($slug);

        if (!$post) {
            // Si l'article n'existe pas, redirect l'utilisateur vers la liste des articles
            return $this->redirectToRoute('articles');
        }

        return $this->render('app/home/show.html.twig', ['post' => $post]);
    }

    /**
     * @param string $slug
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function contact(): Response
    {
        $successMessage = null;
        $errorMessage = null;

        return $this->render(
            'app/home/contact.html.twig',
            [
                'message_success' => $successMessage,
                'message_error' => $errorMessage
            ]
        );
    }



}
