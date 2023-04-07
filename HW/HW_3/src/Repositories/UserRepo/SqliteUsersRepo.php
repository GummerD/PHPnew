<?
namespace GummerD\PHPnew\Repositories\UserRepo;

use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use PDO;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
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
        $statement = $this->connection->prepare(
            "INSERT INTO users (id, username, first_name, last_name) VALUES (:id, :username, :first_name, :last_name)"
        );

        $statement->execute(
            [
                ':id' => (string)$user->getId(), 
                ':username' => $user->getUsername(),
                ':first_name' => $user->getName()->getFirstname(), 
                ':last_name' => $user->getName()->getLastname(),
            ]
        );

    }

    /**
     * Summary of getUuid
     * @param UUID $id
     * @throws UserNotFoundException
     * @return User
     */
    public function getByUserId($id): User
    {   
        $id = new UUID($id);

        $statement = $this->connection->prepare(
            "SELECT * FROM users WHERE id = :id"
        );

        $statement->execute([
            ":id" => (string)$id,
        ]);

        return $this->getResult($statement);
        
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

        return $this->getResult($statement);
    }

    public function getResult(PDOStatement $statement): User
    {

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new UserNotFoundException(
                "Такого пользователя не существует"
            );
        }

        return new User(
            new UUID($result['id']),
            $result['username'],
            new Name(
                $result['first_name'],
                $result['last_name']
            )
        ); 
    }
}