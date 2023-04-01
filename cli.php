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
