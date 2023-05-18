<?
namespace GummerD\PHPnew\Repositories\UserRepo;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UsersExceptionsMamoryRepo;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Summary of MemoryRepoUsers
 */
class MemoryRepoUsers implements UsersRepositoryInterface
{

    /**
     * Summary of data
     * @var array|null
     */
    protected ?array $data;
    private LoggerInterface $logger;

    
    /**
     * Summary of save
     * @param User $user
     * @return void
     */
    public function save(User $user):void
    {
        $this->data[] = $user;
    }

    /**
     * Summary of getAll
     * @return array
     */
    public function getAll(): array
    {
        return $this->data;
    }

    /**
     * Summary of getByUserId
     * @param mixed $id
     * @throws UsersExceptionsMamoryRepo
     * @return User
     */
    public function getByUserId($id): User
    {   
        $id = new UUID((string)$id);
        
        $this->logger->info("Попытка получения информации из MemoryRepoUsers о пользователе с id: {$id} ");

        foreach($this->data as $user)
        {   
            if($id  === $user->getId())
            {
                return $user;
            }
        }

        $this->logger->info("Пользователь в MemoryRepoUsers с id: {$id}  не найден");

        throw new UsersExceptionsMamoryRepo("Нет такого пользователя: {$id}", 404);

    }

    /**
     * Summary of getByUsername
     * @param mixed $username
     * @throws UsersExceptionsMamoryRepo
     * @return User
     */
    public function getByUsername($username): User
    {   
        $this->logger->info("Попытка получения информации из MemoryRepoUsers о пользователе с логином: {$username} .");

        foreach($this->data as $user)
        {   
            if($username  === $user->getUsername())
            {
                return $user;
            }
        }

        $this->logger->info("Пользователь в MemoryRepoUsers с id: {$username}  не найден");

        throw new UsersExceptionsMamoryRepo("Нет такого пользователя: {$username}", 404);
    }

    public function delete($id): void
    {
        
    }

    public function UserExists($username): bool
    {
        return true;
    }
}