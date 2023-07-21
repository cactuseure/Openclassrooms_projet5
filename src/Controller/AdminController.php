<?php
namespace App\Controller;
use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Exception;
use http\Client\Curl\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends AbstractController
{


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function listPosts(): Response
    {
        $postRepository = new PostRepository();
        $posts = $postRepository->getAllPosts();

        return $this->render('/app/admin/list-posts.html.twig', [
            'posts' => $posts,
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function editPost(): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $post = null;

        if (isset($_GET['post_id'])){
            $postRepository = new PostRepository();
            $postId = $_GET['post_id'];
            $post = $postRepository->getPostById($postId);
            if ($post!==null){
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $title = $_POST['title'];
                    $slug = $postRepository->generateSlug($title);
                    $hat = $_POST['hat'];
                    $content = $_POST['content'];
                    if (empty($title) || empty($hat) || empty($content)|| empty($slug)){
                        $errorMessage = 'veuillez remplir tous les champs';
                    } elseif ($postRepository->isSlugIsTaken($slug,true)){
                        $errorMessage = 'Un article possède deja ce titre';
                    }else{
                        $updated_post = new Post($title,$post->getSlug(),$post->getThumbnail(),$hat,$content,$post->getCreatedAt(),new \DateTimeImmutable(),$post->isActive(),$post->getAuthorId(),$post->getCategoryId(),$post->getId());

                        $postRepository->updatePost($updated_post);
                        $successMessage = 'Article modifié avec succès';
                        $post = $updated_post;
                    }
                }
            }else{
                $errorMessage = 'article introuvable';
            }
        }else{
            $errorMessage = 'article introuvable';
        }
        return $this->render('/app/admin/edit-post.html.twig', [
            'post' => $post,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function addPost(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $thumbnail = $_POST['thumbnail'] ?? '';
            $hat = $_POST['hat'] ?? '';
            $content = $_POST['content'] ?? '';
            $postRepository = new PostRepository();
            $slug = $postRepository->generateSlug($title);
            if (empty($title) || empty($hat) || empty($content) || empty($thumbnail)) {
                $errorMessage = 'Veuillez remplir tous les champs du formulaire.';
            } elseif ($postRepository->isSlugIsTaken($slug)){
                $errorMessage = 'Ce titre d\'article est déjà utilisé';
            } else {
                $post = new Post();
                $post->setTitle($title);
                $post->setSlug($slug);
                $post->setThumbnail($thumbnail);
                $post->setHat($hat);
                $post->setContent($content);
                $post->setAuthorId(9);
                $post->setCategoryId(1);
                $post->setCreatedAt(new \DateTimeImmutable());
                $post->setUpdatedAt(new \DateTimeImmutable());
                $post->setIsActive(true);
                $postRepository->createPost($post);
                $successMessage = 'Article ajouté avec succès';
            }
        }
        return $this->render('/app/admin/add-post.html.twig', [
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }

    private function isUserLoggedIn(): bool
    {
        return (isset($_SESSION['user']['id']) && $_SESSION['user']['id']);
    }
    private function isUserLoggedInAdmin(): bool
    {
        return (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'administrateur');
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function deletePost(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $postRepository = new PostRepository();
        if (isset($_GET['post_id']) && $_GET['post_id'] != null){
            $post = $postRepository->getPostById($_GET['post_id']);
            if ($post){
                if ($postRepository->deletePost($_GET['post_id'])) {
                    $successMessage = 'Article : "' . $post->getTitle() . '" a été supprimé avec succès.';
                }else {
                    $errorMessage = ' Erreur lors de la suppression';
                }
            }
        }

        $posts = $postRepository->getAllPosts();

        return $this->render('/app/admin/list-posts.html.twig', [
            'posts' => $posts,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function swapStatus(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;
        $postRepository = new PostRepository();
        if (isset($_GET['post_id']) && $_GET['post_id'] != null){
            $post = $postRepository->getPostById($_GET['post_id']);
            if ($post){
                if (!$postRepository->swapStatus($_GET['post_id'])) {
                    $errorMessage = 'Erreur lors du changement d\'état';
                }

            }else{
                $errorMessage = 'Article introuvable';
            }
        }else{
            $errorMessage = 'Article introuvable';
        }

        $posts = $postRepository->getAllPosts();

        return $this->render('/app/admin/list-posts.html.twig', [
            'posts' => $posts,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function listComments(Request $request): Response
    {
        $commentRepository = new CommentRepository();
        $postRepository = new PostRepository();
        $userRepository = new UserRepository();
        $comments = array_reverse($commentRepository->getAllComments());
        $successMessage = $this->getSuccessMessage($request);
        $errorMessage = $this->getErrorMessage($request);
        $arrayAuthor = array();
        /** @var Comment $comment */
        foreach ($comments as $comment){
            $arrayAuthor[$comment->getId()] = $userRepository->getUserById($comment->getAuthorId())->getUsername();
        }
        $content = $this->twig->render('app/admin/list-comments.html.twig', [
            'comments' => $comments,
            'postRepository' => $postRepository,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
            'arrayAuthor' => $arrayAuthor
        ]);
        return new Response($content);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function listUsers(Request $request): Response
    {
        $userRepository = new UserRepository();
        $users = $userRepository->getUsers();
        $successMessage = $this->getSuccessMessage($request);
        $errorMessage = $this->getErrorMessage($request);
        $content = $this->twig->render('app/admin/list-users.html.twig', [
            'user' => $users,
            'message_success' => $successMessage,
            'message_error' => $errorMessage,
        ]);
        return new Response($content);
    }

    public function getSuccessMessage(Request $request): string|null
    {
        if ($request->isMethod('GET') && $request->query->has('comment_id')) {
            return $request->query->get('comment_id');
        }else{
            return null;
        }
    }
    public function getErrorMessage(Request $request): string|null
    {
        if ($request->isMethod('GET') && $request->query->has('comment_id')) {
            return $request->query->get('comment_id');
        }else{
            return null;
        }
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function removeComment(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;

        $commentRepository = new CommentRepository();

        if ($this->isUserLoggedInAdmin() && $request->isMethod('GET') && $request->query->has('comment_id')){
            $comment_id = $request->query->get('comment_id');
            if ($commentRepository->deleteComment($comment_id)){
                $successMessage = 'Commentaires supprimés avec succès';
            }else{
                $errorMessage = 'Une erreur est survenue';
            }
        }

        $comments = $commentRepository->getAllComments();

        return $this->render('app/admin/list-comments.html.twig', [
            'comments' => $comments,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function approveComment(Request $request): Response
    {
        $successMessage = null;
        $errorMessage = null;

        $commentRepository = new CommentRepository();

        if ($this->isUserLoggedInAdmin() && $request->isMethod('GET') && $request->query->has('comment_id')){
            $comment_id = $request->query->get('comment_id');
            $comment = $commentRepository->getCommentById($comment_id);
            $comment->setApproved(!$comment->getStatus());
            if ($commentRepository->updateComment($comment) && $comment->getStatus()){
                $successMessage = 'Le commentaire est maintenant visible';
                return $this->redirectToRoute('/admin/commentaires',['message_success' => $successMessage]);
            }elseif($commentRepository->updateComment($comment) && !$comment->getStatus()){
                $successMessage = 'Le commentaire est maintenant invisible';
                return $this->redirectToRoute('/admin/commentaires',['message_success' => $successMessage]);
            }else{
                $errorMessage = 'Une erreur est survenue';
            }
        }

        $comments = $commentRepository->getAllComments();

        return $this->render('app/admin/list-comments.html.twig', [
            'comments' => $comments,
            'message_success' => $successMessage,
            'message_error' => $errorMessage
        ]);
    }


}
