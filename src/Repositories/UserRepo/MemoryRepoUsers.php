<?
namespace GummerD\PHPnew\Repositories\UserRepo;

use GummerD\PHPnew\Exceptions\UsersExceptions\UsersExceptionsMamoryRepo;
use GummerD\PHPnew\Models\User;

class MemoryRepoUsers{

    protected ?array $data;

    
    public function save(User $user)
    {
        $this->data[] = $user;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function getUserId($id)
    {
        foreach($this->data as $user)
        {   
            if($id  === $user->getId())
            {
                return $user;
            }
        }

        throw new UsersExceptionsMamoryRepo("Нет такого пользователя: {$id}", 404);
    }
}