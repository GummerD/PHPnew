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
        private PDO $connection
    ){
    } 

    /**
     * Summary of save
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $existUser = $this->UserExists($user->getUsername());

        if($existUser === true)
        {
            throw new UserAlradyExistsException("Пользователь с логином {$user->getUsername()} уже сущесвует");
        }

        $statement = $this->connection->prepare(
            "INSERT INTO users (user_id, username, first_name, last_name) VALUES (:user_id, :username, :first_name, :last_name)"
        );

        $statement->execute(
            [
                ':user_id' => (string)$user->getId(), 
                ':username' => $user->getUsername(),
                ':first_name' => $user->getName()->getFirstname(), 
                ':last_name' => $user->getName()->getLastname(),
            ]
        );
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

        if($result === false){
            throw new UserNotFoundException(
                "Пользователя с таким {$name}: {$variable} не существует. "
            );
        }

        return new User(
            new UUID($result['user_id']),
            $result['username'],
            new Name(
                $result['first_name'],
                $result['last_name']
            )
        ); 
    }

    public function delete($id): void
    {   
        $id = new UUID($id);

        $statement = $this->connection->prepare(
            "DELETE FROM users WHERE user_id = :id" 
        );

        $statement->execute([
            ':id'=>(string)$id,
        ]);
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

        if($result === false){
            return false;
        }

        return true;
    }

}