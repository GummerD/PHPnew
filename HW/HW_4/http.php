<?

require_once(__DIR__ . '/vendor/autoload.php');

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Exceptions\App\AppException;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Actions\Posts\CreatePost;
use GummerD\PHPnew\http\Actions\Posts\DeletePost;
use GummerD\PHPnew\http\Actions\Users\CreateUser;
use GummerD\PHPnew\http\Actions\Users\DeleteUser;
use GummerD\PHPnew\http\Actions\Comments\CreateComment;
use GummerD\PHPnew\http\Actions\Comments\DeleteComment;
use GummerD\PHPnew\http\Actions\Posts\ActionFindPostById;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\Repositories\PostsRepo\SqlitePostsRepo;
use GummerD\PHPnew\http\Actions\Users\ActionFindByUsername;
use GummerD\PHPnew\http\Actions\Comments\ActionFindCommentById;
use GummerD\PHPnew\Repositories\CommentsRepo\SqliteCommentsRepo;

/*
try {

    $request = new Request(
        $_GET,
        $_SERVER
    );

    $connection = new \PDO('sqlite:' . __DIR__ . "/blog.sqlite");

    $userRepo = new SqliteUsersRepo($connection);

    //print_r($connection);


    $actionByUsername = new ActionFindByUsername($userRepo);
    $response = $actionByUsername->handle($request);

    $response->send();
    
} catch (\Exception $ex) {
    echo $ex->getMessage();
}
*/
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
    // Добавили ещё один уровень вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show_by_username' => new ActionFindByUsername(
            new SqliteUsersRepo($connection)
        ),
        '/posts/show_by_id' => new ActionFindPostById(
            new SqlitePostsRepo($connection)
        ),
        '/comments/show_by_id' => new ActionFindCommentById(
            new SqliteCommentsRepo($connection)
        ),

    ],
    'POST' => [
        // Добавили новый маршрут
        '/create/new_user'=> new CreateUser(
            new SqliteUsersRepo($connection)
        ),
        '/create/new_post' => new CreatePost(
            new SqlitePostsRepo($connection),
            new SqliteUsersRepo($connection)
        ),
        '/create/new_comment' => new CreateComment(
            new SqlitePostsRepo($connection),
            new SqliteUsersRepo($connection),
            new SqliteCommentsRepo($connection)
        ),
    ],
    'DELETE' => [
        '/delete/post' => new DeletePost(
            new SqlitePostsRepo($connection)
        ),
        '/delete/user' => new DeleteUser(
            new SqliteUsersRepo($connection)
        ),
        '/delete/comment' => new DeleteComment(
            new SqliteCommentsRepo($connection)
        ),
    ],

];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}


