<?

namespace GummerD\PHPnew\Repositories\UserRepo;

use GummerD\PHPnew\Exceptions\UsersExceptions\UserAlradyExistsException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use PDO;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use InvalidArgumentException;
use PDOStatement;
use Psr\Log\LoggerInterface;

/**
 * Summary of SqliteUsersRepo
 */
class SqliteUsersRepo implements UsersRepositoryInterface
{

    /**
     * Summary of __construct
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger

    ) {
    }

    /**
     * Summary of save
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $existUser = $this->UserExists($user->getUsername());

        if ($existUser === true) {
            $this->logger->warning("Предпринята попытка создать пользователя с уже имеющемся в БД логином: {$user->getUsername()} через SqliteUserssRepo");

            throw new UserAlradyExistsException("Пользователь с логином {$user->getUsername()} уже сущесвует");
        }

        $statement = $this->connection->prepare(
            "INSERT INTO users 
                (
                user_id, 
                username, 
                password, 
                first_name, 
                last_name
                ) 
            VALUES 
                (
                :user_id, 
                :username, 
                :password, 
                :first_name, 
                :last_name
                )
            ON CONFLICT (username) DO UPDATE SET
                first_name = :first_name,
                last_name = :last_name  
            "
        );

        $statement->execute(
            [
                ':user_id' => (string)$user->getId(),
                ':username' => $user->getUsername(),
                ':password' => $user->getPassword(),
                ':first_name' => $user->getName()->getFirstname(),
                ':last_name' => $user->getName()->getLastname(),
            ]
        );

        $this->logger->info("Через SqliteUsersRepo создан новый пользователь с логином: {$user->getUsername()}.");
    }

    public function saveForCommandConsole(User $user): void
    {
        $statement = $this->connection->prepare(
            "INSERT INTO users 
                (
                user_id, 
                username, 
                password, 
                first_name, 
                last_name
                ) 
            VALUES 
                (
                :user_id, 
                :username, 
                :password, 
                :first_name, 
                :last_name
                )
            ON CONFLICT (username) DO UPDATE SET
                first_name = :first_name,
                last_name = :last_name  
            "
        );

        $statement->execute(
            [
                ':user_id' => (string)$user->getId(),
                ':username' => $user->getUsername(),
                ':password' => $user->getPassword(),
                ':first_name' => $user->getName()->getFirstname(),
                ':last_name' => $user->getName()->getLastname(),
            ]
        );

        $this->logger->info("Через SqliteUsersRepo создан новый пользователь с логином: {$user->getUsername()}.");
    }


    /**
     * Summary of getUuid
     * @param  $id
     * @throws UserNotFoundException
     * @return User
     */
    public function getByUserId($user_id): User
    {
        try {
            $user_id = new UUID($user_id);

            $this->logger->info("Через SqliteUsersRepo инициализирован запрос о пользователе с id: {$user_id}.");
        } catch (InvalidArgumentException $e) {
            $e->getMessage();
        }

        $statement = $this->connection->prepare(
            "SELECT * FROM users WHERE user_id = :user_id"
        );

        $statement->execute([
            ":user_id" => (string)$user_id,
        ]);

        return $this->getResult($statement, 'id', $user_id);
    }

    /**
     * Summary of getByUsername
     * @param mixed $username
     * @throws UserNotFoundException
     * @return User
     */
    public function getByUsername($username): User
    {
        $this->logger->info("Через SqliteUsersRepo инициализирован запрос о пользователе с логином: {$username}.");

        $statement = $this->connection->prepare(
            "SELECT * FROM users WHERE username = :username"
        );

        $statement->execute([
            ":username" => (string)$username,
        ]);

        return $this->getResult($statement, 'логином', $username);
    }

    public function getResult(PDOStatement $statement, $name, $variable): User
    {

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Попытка получения информации через SqliteUserssRepo о пользователе с {$name}: {$variable} оказалась не удачной.");
            throw new UserNotFoundException(
                "Пользователя с таким {$name}: {$variable} не существует. "
            );
            return false;
        }

        $this->logger->info(
            "Пользователь с id {$result['user_id']} найден и передан в свой action."
        );

        return new User(
            new UUID($result['user_id']),
            $result['username'],
            $result['password'],
            new Name(
                $result['first_name'],
                $result['last_name']
            )
        );
    }

    public function deleteById($id): void
    {
        $this->logger->info("Инициализирован запрос на удаление пользователя с id: {$id} через SqliteUserssRepo");

        try {
            $id = new UUID($id);
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный запрос в SqliteUsersRepo на удаление пользователь с id: {$id} указан неверно.");
            $e->getMessage();
        }

        $statement = $this->connection->prepare(
            "DELETE FROM users WHERE user_id = :id"
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);
    }

    public function deleteByUsername($username): void
    {
        $this->logger->info("Инициализирован запрос на удаление пользователя с id: {$username} через SqliteUserssRepo");

        $statement = $this->connection->prepare(
            "DELETE FROM users WHERE username = ?"
        );

        $statement->execute([$username]);
    }

    public function UserExists($username): bool
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM users WHERE username = :username"
        );

        $statement->execute([
            ':username' => $username
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return false;
        }

        return true;
    }
}
