<?

namespace GummerD\PHPnew\http\Authentication;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Exceptions\http\AuthException;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Interfaces\Authentication\PasswordAuthenticationInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {

        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
            $id = $user->getId();
            $hash = hash('sha256',$id . $password);
            var_dump($hash);
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if ($hash !== $user->getPassword()) {
            throw new AuthException('Неправльный пароль');
        }

        return $user;
    }
}
