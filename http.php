<?
use GummerD\PHPnew\http\Request;

use GummerD\PHPnew\Container\DIContainer;
use GummerD\PHPnew\Exceptions\App\AppException;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Actions\Likes\CreateLike;
use GummerD\PHPnew\http\Actions\Likes\DeleteLike;
use GummerD\PHPnew\http\Actions\Posts\CreatePost;
use GummerD\PHPnew\http\Actions\Posts\DeletePost;
use GummerD\PHPnew\http\Actions\Users\CreateUser;
use GummerD\PHPnew\http\Actions\Users\DeleteUser;
use GummerD\PHPnew\http\Actions\Comments\CreateComment;
use GummerD\PHPnew\http\Actions\Comments\DeleteComment;
use GummerD\PHPnew\http\Actions\Likes\ActionFindLikeById;
use GummerD\PHPnew\http\Actions\Posts\ActionFindPostById;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\Repositories\PostsRepo\SqlitePostsRepo;
use GummerD\PHPnew\http\Actions\Users\ActionFindByUsername;
use GummerD\PHPnew\http\Actions\Comments\ActionFindCommentById;
use GummerD\PHPnew\Repositories\CommentsRepo\SqliteCommentsRepo;
use GummerD\PHPnew\http\Actions\Likes\ActionFindAllPostsByLikeId;
use Psr\Log\LoggerInterface;

$container = require(__DIR__ . '/bootstrap.php');

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show_by_username' => ActionFindByUsername::class,
        '/posts/show_by_id' => ActionFindPostById::class,
        '/comments/show_by_id' => ActionFindCommentById::class,
        '/likes/show_by_id' => ActionFindLikeById::class,
        '/likes/show_all_liles_by_post_id' => ActionFindAllPostsByLikeId::class,

    ],
    'POST' => [
        '/create/new_user'=> CreateUser::class,
        '/create/new_post' => CreatePost::class,
        '/create/new_comment' => CreateComment::class,
        '/create/new_like' => CreateLike::class,
    ],
    'DELETE' => [
        '/delete/post' => DeletePost::class,
        '/delete/user' => DeleteUser::class,
        '/delete/comment' => DeleteComment::class,
        '/delete/like' => DeleteLike::class
    ],

];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = 'Не указан верный action или метод для HTTP (GET, POST, DELETE и т.д.)';
    $logger->warning($message);
    (new ErrorResponse($message))->send();
    return;
}



$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    $logger->warning($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}


