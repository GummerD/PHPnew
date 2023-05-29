<?

namespace GummerD\PHPnew\Repositories\UserRepo\ForTest;

use PDO;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class DummyUserRepository implements UsersRepositoryInterface
{

    /**
     * Summary of save
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        // TODO:Implements save() method.
    }

    /**
     * Summary of getUuid
     * @param UUID $id
     * @throws UserNotFoundException
     */
    public function getByUserId($id): User
    {
        $id = new UUID($id);

        throw new UserNotFoundException("Пользователя с таким id:{$id} не существует.");
    }

    /**
     * Summary of getByUsername
     * @param mixed $username
     * @throws UserNotFoundException
     */
    public function getByUsername($username): User
    {
        return new User(
            UUID::random(),
            $username,
            'some_password',
            new Name(
                'Ivan',
                'Ivanov'
            )
        );
    }

    public function UserExists($username): bool
    {
        return true;
    }

    public function delete($username): void
    {

    }


}
