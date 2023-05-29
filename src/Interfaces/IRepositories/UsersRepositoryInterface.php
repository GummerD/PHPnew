<?
namespace GummerD\PHPnew\Interfaces\IRepositories;

use GummerD\PHPnew\Models\User;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function saveForCommandConsole(User $user): void;
    public function getByUserId($id): User;
    public function getByUsername($username): User;
    public function deleteById($id): void;
    public function deleteByUsername($username): void;
    public function UserExists($username): bool;
}