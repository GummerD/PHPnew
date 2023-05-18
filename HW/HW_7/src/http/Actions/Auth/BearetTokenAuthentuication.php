<?

namespace GummerD\PHPnew\http\Actions\Auth;

use DateTimeImmutable;
use DateTimeInterface;
use GeekBrains\Blog\Repositories\AuthTokensRepository\AuthTokenNotFoundException;
use GummerD\PHPnew\Exceptions\http\AuthException;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Interfaces\Authentication\AuthTokensRepositoryInterface;
use GummerD\PHPnew\Interfaces\Authentication\TokenAuthenticationInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Models\AuthToken;
use GummerD\PHPnew\Models\User;
use Psr\Log\LoggerInterface;

class BearetTokenAuthentuication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        protected AuthTokensRepositoryInterface $authTokensRepository,
        protected UsersRepositoryInterface $sqliteUsersRepo,
        protected LoggerInterface $logger
    ) {
    }

    public function user(Request $request): User
    {   
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Неправильный header:{$header}");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $auth = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            throw new AuthException($e->getMessage("Неправильный token: {$token}"));
        }

        $timeNow = (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
        $expiresOn= $auth->expiresOn()->format(DateTimeInterface::ATOM);

        if ($expiresOn <= $timeNow) {
            $this->logger->warning("У пользователя с id:{$auth->userId()} истекло время пользования token:{$expiresOn}");
            throw new AuthException("У пользователя с id:{$auth->userId()} истекло время пользования token:{$expiresOn}");
        }

        $userId = $auth->userId();

        return $this->sqliteUsersRepo->getByUserId($userId);
    }
}
