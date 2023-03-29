<?
namespace GummerD\PHPnew\Repositories\UserRepo;

use PDO;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;

/**
 * Summary of SqliteUsersRepo
 */
class SqliteUsersRepo
{   

    /**
     * Summary of __construct
     * @param PDO $connection
     */
    public function __construct(
        private \PDO $connection
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
            "INSERT INTO users (id, first_name, last_name) VALUES (:id, :first_name, :last_name)"
        );

        $statement->execute(
            [
                ':id' => (string)$user->getId(), 
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
    public function getUuid($id): User
    {   
        $id = new UUID($id);

        $statement = $this->connection->prepare(
            "SELECT * FROM users WHERE id = :id"
        );

        $statement->execute([
            ":id" => (string)$id,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new UserNotFoundException(
                "Пользователя с таким id:{$id} не существует."
            );
        }

        return new User(
            new UUID($result['id']),
            new Name(
                $result['first_name'],
                $result['last_name']
            )
        );
    }
}