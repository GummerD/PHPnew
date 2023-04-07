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
    $userRepo = new SqliteUsersRepo($connection);

    // переменная для сущности User:
    $user =  new User(
        $uuid,
        $facker->userName(),
        new Name(
            $facker->firstName(),
            $facker->lastName()
        )
    );

    // сохранение в таблицу users данных о новом пользователе:
    $userRepo->save($user);
    
} catch (\Exception $ex) {
    echo $ex->getMessage();
}

try {
    // Сохранение в память.
    // переменная для сущности User:
    $userInMenory =  new User(
        $uuid,
        $facker->userName(),
        new Name(
            $facker->firstName(),
            $facker->lastName()
        )
    );

    // сохранение пользователя в память:
    $userRepoInMemory = new MemoryRepoUsers($userInMenory);
} catch (\Exception $ex) {
    echo $ex->getMessage();
}


/*---------------------------------------------- POST -----------------------------------------------*/


try {
    // подключение репозитория Post к БД
    $postRepo = new SqlitePostsRepo($connection);

    // создание постов в БД
    $post = new Post(
        UUID::random(),
        UUID::random(),
        $facker->text(10),
        $facker->text(30),
    );

    // сохранение в таблицу posts данных о новом пользователе:
    $postRepo->save($post);

} catch (\Exception $ex) {
    echo $ex->getMessage();
}


/*---------------------------------------------- COMMENTS -----------------------------------------------*/

try {
    // подключение репозитория к БД
    $commentRepo = new SqliteCommentsRepo($connection);

    // создание комментариев в БД
    $comment = new Comment(
        UUID::random(),
        UUID::random(),
        UUID::random(),
        $facker->text(30)
    );

    // сохранение в таблицу comments данных о новом пользователе:
    $commentRepo->save($comment);

} catch (\Exception $ex) {
    echo $ex->getMessage();
}
