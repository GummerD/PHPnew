<?

use Faker\Factory;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Repositories\UserRepo\MemoryRepoUsers;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\Repositories\PostsRepo\SqlitePostsRepo;
use GummerD\PHPnew\Repositories\CommentsRepo\SqliteCommentsRepo;

require_once __DIR__ . "/vendor/autoload.php";

//библиотека рандомных данных:
$facker = Factory::create('ru_Ru');

//подключение к DB:
$connection = new \PDO('sqlite:' . __DIR__ . "/blog.sqlite");

//сущность - UUID для генерации id:
$uuid = UUID::random();

/* ----------------------------- USER ---------------------------------*/
try {
    // Подключение репозитотрия к БД:
    //$userRepo = new SqliteUsersRepo($connection);

    //echo $userRepo->getByUserId('5b095d52-69a1-4f69-98d2-a8c1d9fce98f');
    //echo $userRepo->getByUsername('pavel000');
} catch (\Exception $ex) {
    echo $ex->getMessage();
}


/* ----------------------------- POST ---------------------------------*/

try {
    // Подключение репозитотрия к БД:

    //$userRepo = new SqliteUsersRepo($connection);
    //$postRepo = new SqlitePostsRepo($connection);

    // получение экземпляра пользователя из БД для использования его в посте
    //$user = $userRepo->getByUserId(new UUID('25474b66-690a-4005-a461-acc1fb1a2b33'));
    //print_r($user);

    // связываение пользователя и поста:
    /*$post = new Post(
        UUID::random(),
        $user,
        $facker->text(10),
        $facker->text(50),
    );*/

    //$postRepo->save($post);
    //$postRepo->getAllPosts();
    //echo $postRepo->getPostById('6f188630-4537-4d19-a04a-fb3ebac499a3');
    //echo $postRepo->getPostByTitle('Expedita.');
} catch (\Exception $ex) {
    echo $ex->getMessage();
}


try {
    // Подключение репозитотрия к БД:

    $userRepo = new SqliteUsersRepo($connection);
    $postRepo = new SqlitePostsRepo($connection);
    $commentRepo = new SqliteCommentsRepo($connection);

    // получение экземпляра пользователя из БД для использования его в посте
    $user = $userRepo->getByUserId('8356b162-fb45-4b8f-8484-bb459751587b');
    $post = $postRepo->getPostById('c76ce9e8-786d-45df-b838-34e944447e70');
    //print_r($user);
    //print_r($post);

    // связываение пользователя и поста:
    $comment = new Comment(
        UUID::random(),
        $user,
        $post,
        $facker->text(50),
    );

    $commentRepo->save($comment);
    $commentRepo->getAllComments();
    //echo $commentRepo->getCommentById('5af0aad5-b937-4266-88f8-882ec177fef7');
    //echo $postRepo->getPostByTitle('Expedita.');
} catch (\Exception $ex) {
    echo $ex->getMessage();
}
