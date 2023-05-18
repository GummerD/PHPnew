<?
namespace GummerD\PHPnew\http\Identification;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Exceptions\http\AuthException;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\http\Actions\Interfaces\IdentificationInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class JsonBodyIdentificationUserById implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function user (Request $request): User
    {
        try {
            $user_id = new UUID($request->jsonBodyField('user_id'));
        } catch (HttpException | InvalidArgumentException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->usersRepository->getByUserId($user_id);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
