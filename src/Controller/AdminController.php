<?php
namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();

       /* if (!$this->isUserLoggedIn()) {
            header('Location: /');
            exit;
        }*/
    }

    public function login(): void
    {
        if ($this->isUserLoggedIn()) {
            header('Location: /admin/panel');
            exit;
        }

        $this->render('admin/login.html.twig');
    }

    public function panel(): void
    {
        $this->render('admin/panel.html.twig');
    }

    public function edit(): void
    {
        $articleId = $_GET['id'];

        $article = $this->entityManager->getRepository(Article::class)->find($articleId);

        if (!$article) {
            header('HTTP/1.0 404 Not Found');
            $this->render('404.html.twig');
            exit;
        }

        $this->render('admin/edit.html.twig', [
            'article' => $article,
        ]);
    }

    public function create(): void
    {
        $this->render('admin/create.html.twig');
    }

    public function editPost(): void
    {
        $postId = $_GET['id'] ?? null;

        if (!$postId) {
            header('Location: /admin/edit');
            exit;
        }

        $post = $this->getPostById($postId);

        if (!$post) {
            header('Location: /admin/edit');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';

            $errors = $this->validatePostData($title, $content);

            if (count($errors) === 0) {

                $success = $this->updatePost($postId, $title, $content);

                if ($success) {
                    header('Location: /admin/panel');
                    exit;
                } else {
                    $errors[] = "Une erreur est survenue lors de la mise à jour de l'article.";
                }
            }

            $this->render('admin/edit_post.html.twig', [
                'post' => $post,
                'errors' => $errors,
            ]);

        } else {
            $this->render('admin/edit_post.html.twig', [
                'post' => $post,
                'errors' => [],
            ]);
        }
    }

    private function isUserLoggedIn(): bool
    {
        return (isset($_SESSION['user_id']) && $_SESSION['user_id']);
    }

    public function addNew()
    {
    }
}
