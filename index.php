<?php
// index.php
require_once __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

session_start();

require_once __DIR__ . '/bootstrap.php';

// Créez une instance de Request en utilisant les superglobales PHP
$request = Request::createFromGlobals();

// Obtenez l'URL demandée à partir de la requête
$url = $request->getPathInfo();

// Instanciation des contrôleurs
$homeController = new \App\Controller\HomeController();
$postController = new \App\Controller\PostController();
$userController = new \App\Controller\UserController();

// Récupérer les données POST et GET
$postData = $request->request->all();
$getData = $request->query->all();

// Ajouter les données POST et GET à la requête
$request = $request->duplicate($getData, $postData);

// Définissez vos routes et associez-les aux contrôleurs correspondants
$routes = [
    '/' => [$homeController, 'index'],
    '/articles' => [$postController, 'index'],
    '/connexion' => [$userController, 'login'],
    '/inscription' => [$userController, 'register'],
    '/mon-compte' => [$userController, 'account'],
    '/edit-profil' => [$userController, 'editProfile'],
    '/deconnexion' => [$userController, 'logout'],
    '/forget-password' => [$userController, 'forgetPassword'],
    '/password-reset-requested' => [$userController, 'passwordResetRequested'],
    '/article' => [$postController, 'show'], // Nouvelle route pour afficher un post
];

// Vérifiez si l'URL correspond à une route définie
if (isset($routes[$url])) {
    // Exécutez la méthode correspondante du contrôleur
    $controller = $routes[$url][0];
    $action = $routes[$url][1];
    $response = $controller->$action($request); // Passer la requête en argument
} elseif (str_starts_with($url, '/article/')) {
    // La route correspond à /article/slug
    $slug = substr($url, strlen('/article/'));
    try {
        $response = $postController->show($slug, $request); // Passer la requête en argument
    } catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
        $response = new Response('Internal Server Error', 500);
    }
} else {
    // Afficher une page 404
    $response = new Response('Page not found', 404);
}

if (!$response instanceof Response) {
    throw new \RuntimeException('The controller action must return an instance of Response.');
}

// Envoyez la réponse au client
$response->send();
