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
    $userRepo = new SqliteUsersRepo($connection);
    $userRepo->save($user);
    echo "Sqlite: {$user}" . PHP_EOL;

    // получение информации о пользователе из таблицы users по UUID:
    echo "Найден пользователь: " . $userRepo->getByUserId(
        'dbf834e4-8b45-42a7-b146-308faff940d1'
    ) . PHP_EOL;

    // поиск пользователя по логину:
    $login = 'gleb93';
    echo "Найден пользователь по логину {$login}: " . $userRepo->getByUsername(
        'gleb93'
    ) . PHP_EOL;

    // проверка на исключение (неправильный формат):
    echo $userRepo->getByUserId('096d65d9');
} catch (\Exception $ex) {
    echo $ex->getMessage();
}

try {
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
    $userRepoInMemory->save($userInMenory);
    $userInMenory = $userRepoInMemory->getAll();
    print_r($userInMenory);
} catch (\Exception $ex) {
    echo $ex->getMessage();
}


/*---------------------------------------------- POST -----------------------------------------------*/


try {
    // подключение репозитория Post к БД
    $postRepo = new SqlitePostsRepo($connection);

    // создание постов в БД
    for ($i = 0; $i <= 5; $i++) {
        $post = new Post(
            UUID::random(),
            UUID::random(),
            $facker->text(10),
            $facker->text(30),
        );
        $postRepo = new SqlitePostsRepo($connection);
        $postRepo->save($post);
    }

    // вывод всех постов
    $postRepo->getAllPosts();
    // вывод поста по его id
    $id = 'c2ce9660-75bb-4007-a9b7-fcb4dd77b37e';
    echo "Найден пост по id: {$id}" . $postRepo->getPostById(new UUID($id)) . PHP_EOL;
    // вывод поста по его заголовку
    $title = 'Quia.';
    echo "Найден пост по заголовку: {$title}" . $postRepo->getPostByTitle($title) . PHP_EOL;
} catch (\Exception $ex) {
    echo $ex->getMessage();
}


/*---------------------------------------------- COMMENTS -----------------------------------------------*/

try {
    // подключение репозитория к БД
    $commentRepo = new SqliteCommentsRepo($connection);

    // создание комментариев в БД
    for ($i = 0; $i <= 5; $i++) {
        $comment = new Comment(
            UUID::random(),
            UUID::random(),
            UUID::random(),
            $facker->text(30)
        );

        $commentRepo->save($comment);
    }

    // вывод всех комментариев
    echo $commentRepo->getAllComments();
    // вывод комменатрия по его id
    $id = '69c797a4-aee8-4791-be03-79dffd19fba6';
    echo "Найден комментарий по id: {$id}" . $commentRepo->getCommentById(new UUID($id)) . PHP_EOL;
    // вывод комменатрия по id  пользователя
    $owner_id = '0ed6bf9b-1ba4-4a19-8b39-c9a5104dc5a3';
    echo "Найден комментарий пользователя с id: {$owner_id}" . $commentRepo->getCommentByOwner_id(new UUID($owner_id)) . PHP_EOL;
} catch (\Exception $ex) {
    echo $ex->getMessage();
}
