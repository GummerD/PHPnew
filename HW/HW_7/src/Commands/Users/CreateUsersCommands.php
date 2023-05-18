<?

namespace GummerD\PHPnew\Commands\Users;

use Psr\Log\LoggerInterface;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\CommadsExceptions\CommandException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

/**
 * Summary of CreateUsersCommands
 * 
 * Метод для ввода данных в таблицу users из командной строки.
 */
class CreateUsersCommands
{

    /**
     * Summary of __construct
     * @param UsersRepositoryInterface $userRepository
     */
    public function __construct(
        private UsersRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Summary of handle
     * @param Arguments $argument
     * @throws CommandException
     * @return void
     */
    public function handle(Arguments $argument): void
    {   
        $this->logger->info('Создание нового пользователя через командную строку');

        $username = $argument->get('username');

        $password = hash('sha256', $argument->get('password'));

        
        if ($this->userExists($username)) {
            throw new CommandException("Пользователь с таким {$username} логиномы уже существует");
        }

        $this->userRepository->save(
            new User(
                UUID::random(),
                $username,
                $password,
                new Name(
                    $argument->get('first_name'),
                    $argument->get('last_name')
                )
            )
        );

        $this->logger->info("Создан новый пользователь через командню строку под логином:{$username}");
    }

    /**
     * Summary of userExists
     * @param string $username
     * @return bool
     */
    private function userExists(string $username): bool
    {
        try {
            $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException) {
           return false;
        }
        return true;
        
    }
    
}
