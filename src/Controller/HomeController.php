<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Contrôleur pour la page d'accueil du site.
 */
class HomeController extends AbstractController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Affiche la page d'accueil du site.
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): Response
    {
        return $this->render('app/home/index.html.twig');
    }
}
