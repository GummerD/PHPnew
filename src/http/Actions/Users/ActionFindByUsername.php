<?

namespace GummerD\PHPnew\http\Actions\Users;

use Psr\Log\LoggerInterface;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\http\Identification\JsonBodyIdentificationUserByUsername;


class ActionFindByUsername implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private JsonBodyIdentificationUserByUsername $identification,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        // ввел идентификатор
        $user = $this->identification->user($request);

        // ввел логер
        $this->logger->info("
            Запрос на получение информации о 
            пользователе c логином: {$user->getUsername()}
        ");

        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'name' => $user->getName()->getFirstname() . ' ' . $user->getName()->getLastname(),
        ]);
    }
}
