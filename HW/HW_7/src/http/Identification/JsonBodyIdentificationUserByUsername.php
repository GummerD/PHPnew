<?
namespace GummerD\PHPnew\http\Identification;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Exceptions\http\AuthException;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\http\Actions\Interfaces\IdentificationInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class JsonBodyIdentificationUserByUsername implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function user (Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException | InvalidArgumentException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
