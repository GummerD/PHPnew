<?

use Psr\Log\LoggerInterface;
use GummerD\PHPnew\Commands\Users\Arguments;
use GummerD\PHPnew\Commands\Users\CreateUsersCommands;
use GummerD\PHPnew\Exceptions\CommadsExceptions\CommandException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;

$container = require_once __DIR__ . "/bootstrap.php";

$command = $container->get(CreateUsersCommands::class);

$logger = $container->get(LoggerInterface::class);

var_dump($_SERVER['ANOTHER_VARIABLE']);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (CommandException | UserNotFoundException $e) {
    $logger->warning($e->getMessage(), ['exception' => $e]);
}