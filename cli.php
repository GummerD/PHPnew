<?

use Faker\Factory;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Repositories\UserRepo\MemoryRepoUsers;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;

require_once __DIR__ . "/vendor/autoload.php";

/* ----------------------------- USER ---------------------------------*/

try {

    //подключение к DB:
    $connection = new \PDO('sqlite:' . __DIR__ . "/blog.sqlite");

    //библиотека рандомных данных:
    $facker = Factory::create('ru_Ru');

    //сущность - UUID для генерации id:
    $uuid = UUID::random();

    // переменная для сущности User:
    $user =  new User(
        $uuid,
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
    echo $userRepo->getUuid('096d65d9-f42c-43c2-957d-48f19515dcce') . PHP_EOL;
    // проверка на исключение.
    echo $userRepo->getUuid('096d65d9');
} catch (\Exception $ex) {
    echo $ex->getMessage();
}

try {

    $userInMenory =  new User(
        $uuid,
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
