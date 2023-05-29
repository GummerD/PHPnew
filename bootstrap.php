<?

use Dotenv\Dotenv;
use Monolog\Logger;
use Faker\Provider\Lorem;
use Psr\Log\LoggerInterface;
use Faker\Provider\ru_RU\Text;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Internet;
use Monolog\Handler\StreamHandler;
use GummerD\PHPnew\Container\DIContainer;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\Repositories\LikesRepo\SqliteLikesRepo;
use GummerD\PHPnew\Repositories\PostsRepo\SqlitePostsRepo;
use GummerD\PHPnew\http\Authentication\PasswordAuthentication;
use GummerD\PHPnew\http\Actions\Auth\BearetTokenAuthentuication;
use GummerD\PHPnew\Repositories\CommentsRepo\SqliteCommentsRepo;
use GummerD\PHPnew\http\Actions\Interfaces\IdentificationInterface;
use GummerD\PHPnew\Interfaces\Authentication\AuthenticationInterface;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Repositories\TokenRepo\SqliteAuthTokensRepository;
use GummerD\PHPnew\http\Identification\JsonBodyIdentificationUserById;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\Authentication\TokenAuthenticationInterface;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\Authentication\AuthTokensRepositoryInterface;
use GummerD\PHPnew\Interfaces\Authentication\PasswordAuthenticationInterface;

require_once(__DIR__ . '/vendor/autoload.php');

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
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

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepo::class
);

$logger = new Logger('blog');

if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/blog.log')) 
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.info.log',
            level: Logger::INFO,
            bubble: false
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.warning.log',
            level: Logger::WARNING,
            bubble: false
        ));
}

if ($_SERVER['LOG_TO_CONSOLE'] === 'yes') {
    $logger->pushHandler(new StreamHandler('php://stdout'));
}

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyIdentificationUserById::class,
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearetTokenAuthentuication::class
);

$faker = new \Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
    \Faker\Generator::class,
    $faker
);

return $container;
