<?

use Faker\Factory;
use GummerD\PHPnew\Commands\Users\Arguments;
use GummerD\PHPnew\Commands\Users\CreateUsersCommands;
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
$sqlUserRepo = new SqliteUsersRepo($connection);

$command_str = new CreateUsersCommands($sqlUserRepo);

try {
    
    // php comstr.php username=ivanoff_1 first_name=petrovich last_name=ofchanikov
    
    $command_str->handle(Arguments::fromFrgv($argv));

} catch (\Exception $ex) {
    echo $ex->getMessage();
}