<?

namespace GummerD\PHPnew\Repositories\TokenRepo;

use PDOException;
use DateTimeImmutable;
use DateTimeInterface;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\AuthToken;
use GummerD\PHPnew\Exceptions\Auth\AuthTokensRepositoryException;
use GummerD\PHPnew\Interfaces\Authentication\AuthTokensRepositoryInterface;
use GeekBrains\Blog\Repositories\AuthTokensRepository\AuthTokenNotFoundException;
use Psr\Log\LoggerInterface;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private \PDO $connection,
        private LoggerInterface $logger
    ) {
    }

    public function save(AuthToken $authToken): void
    {
        $query = <<<SQL
            INSERT INTO tokens (token, user_id, expires_on) 
                VALUES (:token, :user_id, :expires_on)
                    ON CONFLICT (token) DO UPDATE SET
                    expires_on = :expires_on
            SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => (string)$authToken->token(),
                ':user_id' => (string)$authToken->userId(),
                ':expires_on' => $authToken->expiresOn()
                    ->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }

        if (false === $result) {
            throw new AuthTokenNotFoundException("Не могу найти токен: $token");
        }

        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_id']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (\Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getId($user_id): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE user_id = ?'
            );
            $statement->execute([$user_id]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }

        if (false === $result) {
            throw new AuthTokenNotFoundException("Не могу найти пользователя c id: $user_id");
        }

        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_id']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (\Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function updateTokenExpiresOff(string $token): void
    {   
        
        $statement = $this->connection->prepare(
            "UPDATE tokens 
                SET expires_on = :expires_on
            WHERE token = :token"
        );

        $statement->execute(
            [
                ":token" => $token,
                ":expires_on" => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)
            ]
        );

        $this->logger->info("Срок использлвания токена: {$token} был аннулирован");
    }
}
