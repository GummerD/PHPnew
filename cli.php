<?

use Symfony\Component\Console\Application;
use GummerD\PHPnew\Commands\Likes\LikeCreate;
use GummerD\PHPnew\Commands\Likes\LikeDelete;
use GummerD\PHPnew\Commands\Posts\PostDelete;
use GummerD\PHPnew\Commands\Users\DeleteUser;
use GummerD\PHPnew\Commands\Users\UpdateUser;
use GummerD\PHPnew\Commands\Users\UserCreate;
use GummerD\PHPnew\Commands\Posts\PostsCreate;
use GummerD\PHPnew\Commands\PopulateDB\PopulateDB;
use GummerD\PHPnew\Commands\Comments\CommentCreate;
use GummerD\PHPnew\Commands\Comments\CommentDelete;



$container = require_once __DIR__ . "/bootstrap.php";

$application = new Application();

$commandsClasses = [
    UserCreate::class,
    DeleteUser::class,
    UpdateUser::class,
    PostsCreate::class,
    PostDelete::class,
    CommentCreate::class,
    CommentDelete::class,
    LikeCreate::class,
    LikeDelete::class,
    PopulateDB::class
];

//тестовые данные для нового пользователя: Some_User_123 some_possword Петров Сидоро
//тестовые данные для новой статьи: Петро some_title_11 some_text_11

foreach ($commandsClasses as $commandClass) {

    $command = $container->get($commandClass);

    $application->add($command);
}

$application->run();
