<?

use Faker\Factory;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;

require_once __DIR__ . "/vendor/autoload.php";

//подключение к DB:
$connection = new \PDO('sqlite:'. __DIR__ . "/blog.sqlite");

// подключенная библиотека рандомных данных:
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
echo $user;