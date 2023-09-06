<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    protected Environment $twig;

    public function __construct()
    {
        // Configure le moteur de template Twig
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader, [
            'debug' => true,
        ]);
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addExtension(new DebugExtension());
    }

    /**
     * Rend un template Twig avec les paramètres fournis.
     *
     * @param string $view Le chemin vers le template à rendre (par exemple, 'app/home/contact.html.twig')
     * @param array $parameters Les données passées au template pour l'affichage (variables, messages d'erreur, etc.)
     * @return Response La réponse HTTP contenant le contenu HTML du template rendu
     * @throws RuntimeError|SyntaxError|LoaderError
     */
    protected function render(string $view, array $parameters = []): Response
    {
        $content = $this->twig->render($view, $parameters);

        return new Response($content);
    }

    /**
     * Redirige vers une autre route avec éventuellement des paramètres.
     *
     * @param string $route L'URL de la route de destination (par exemple, '/contact')
     * @param array $parameters Les éventuels paramètres de la route (par exemple, ['id' => 1])
     * @return RedirectResponse La réponse HTTP pour la redirection
     */
    protected function redirectToRoute(string $route, array $parameters = []): RedirectResponse
    {
        $url = $this->generateUrl($route, $parameters);
        return new RedirectResponse($url);
    }

    /**
     * Génère l'URL d'une route avec éventuellement des paramètres.
     *
     * @param string $route L'URL de la route à générer (par exemple, '/contact')
     * @param array $parameters Les éventuels paramètres de la route (par exemple, ['id' => 1])
     * @return string L'URL générée sous forme de chaîne de caractères
     */
    protected function generateUrl(string $route, array $parameters = []): string
    {
        $url = '/' . ltrim($route, '/');
        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }
        return $url;
    }
}