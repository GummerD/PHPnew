<?

use GummerD\PHPnew\Container\DIContainer;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\Repositories\PostsRepo\SqlitePostsRepo;
use GummerD\PHPnew\Repositories\CommentsRepo\SqliteCommentsRepo;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;

require_once (__DIR__ . '/vendor/autoload.php');

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepo::class
);

$container->bind(
    PostsRepositoriesInterface::class,
    SqlitePostsRepo::class
);

$container->bind(
    CommentsRepositoriesInterface::class,
    SqliteCommentsRepo::class
);

return $container;