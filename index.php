<?php
// index.php

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

session_start();

// Créer une instance de Request
$request = Request::createFromGlobals();

// Obtient l'URL demandée à partir de la requête
$url = $request->getPathInfo();

$homeController = new \App\Controller\HomeController();
$postController = new \App\Controller\PostController();
$userController = new \App\Controller\UserController();
$adminController = new \App\Controller\AdminController();
$commentRepository = new \App\Repository\CommentRepository();
$postRepository = new \App\Repository\PostRepository();
$commentController = new \App\Controller\CommentController($commentRepository, $postRepository);

// Récupère les données POST et GET
$postData = $request->request->all();
$getData = $request->query->all();

// Ajoute les données POST et GET à la requête
$request = $request->duplicate($getData, $postData);

$routes = [
    '/' => [$homeController, 'index'],
    '/articles' => [$postController, 'index'],
    '/comment_post' => [$commentController, 'comment_post'],
    '/connexion' => [$userController, 'login'],
    '/inscription' => [$userController, 'register'],
    '/mon-compte' => [$userController, 'account'],
    '/edit-password' => [$userController, 'editPassword'],
    '/edit-profil' => [$userController, 'editProfile'],
    '/deconnexion' => [$userController, 'logout'],
    '/contact' => [$homeController, 'contact'],
    '/forget-password' => [$userController, 'forgetPassword'],
    '/reset-password' => [$userController, 'resetPassword'],
    '/password-reset-requested' => [$userController, 'passwordResetRequested'],
    '/article' => [$postController, 'show'],
    '/admin/articles' => [$adminController, 'listPosts'],
    '/admin/commentaires' => [$adminController, 'listComments'],
    '/admin/utilisateurs' => [$adminController, 'listUsers'],
    '/admin/user-toggle-role' => [$adminController, 'swapUserRole'],
    '/admin/user-toggle-status' => [$adminController, 'swapUserStatus'],
    '/admin/edit-post' => [$adminController, 'editPost'],
    '/admin/add-post' => [$adminController, 'addPost'],
    '/admin/remove-post' => [$adminController, 'deletePost'],
    '/admin/remove-comment' => [$adminController, 'removeComment'],
    '/admin/toggle-post' => [$adminController, 'swapStatus'],
    '/admin/approve-comment' => [$adminController, 'approveComment'],
];

// Vérifie si l'URL correspond à une route définie
if (isset($routes[$url])) {
    // Exécute la méthode correspondante du contrôleur
    $controller = $routes[$url][0];
    $action = $routes[$url][1];
    $response = $controller->$action($request); // Passe la requête en argument
} elseif (str_starts_with($url, '/article/')) {
    // route = /article/slug
    $slug = substr($url, strlen('/article/'));
    try {
        $response = $postController->show($slug, $request);
    } catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
        $response = new Response('Internal Server Error', 500);
    }
} else {
    // page 404
    $response = new Response('Page not found', 404);
}

if (!$response instanceof Response) {
    throw new \RuntimeException('The controller action must return an instance of Response.');
}

// Envoie la réponse au client
$response->send();