<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Symfony\Bridge\Twig\Extension\DumpExtension;
use Twig\Extension\DebugExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractController
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader, [
            'debug' => true,
        ]);
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addExtension(new DebugExtension());
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    protected function render(string $view, array $parameters = []): Response
    {
        // Ajoute la notification aux paramètres si elle est présente dans la session
        if (isset($_SESSION['notification'])) {
            $parameters['notification'] = $_SESSION['notification'];
            unset($_SESSION['notification']); // Supprime la notification de la session après l'avoir récupérée
        }

        $content = $this->twig->render($view, $parameters);

        return new Response($content);
    }

    protected function redirectToRoute(string $route, array $parameters = []): RedirectResponse
    {
        $url = $this->generateUrl($route, $parameters);
        return new RedirectResponse($url);
    }

    protected function generateUrl(string $route, array $parameters = []): string
    {
        $url = '/' . ltrim($route, '/');
        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }
        return $url;
    }
}
