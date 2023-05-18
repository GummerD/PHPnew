<?
namespace GummerD\PHPnew\Models;

use DateTimeImmutable;
use GummerD\PHPnew\Models\UUID;

class AuthToken
{
    public function __construct(
        private string $token,
        private UUID $userId,
        private DateTimeImmutable $expiresOn
    ) {
    }

    public function token(): string
    {
        return $this->token;
    }

    public function userId(): UUID
    {
        return $this->userId;
    }

    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }
}
