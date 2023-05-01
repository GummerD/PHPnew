<?

namespace GummerD\PHPnew\http\Actions\Users;

use Psr\Log\LoggerInterface;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\http\Identification\JsonBodyIdentificationUserByUsername;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class DeleteUser implements ActionInterface
{
    public function __construct(
        private JsonBodyIdentificationUserByUsername $identification,
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        // ввел идентификатор
        $user = $this->identification->user($request);

        try {
            $this->usersRepository->delete($user->getId());
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // ввел логирование на удаление пользователя
        $this->logger->info("Пользователь c логиом: {$user->getUsername()} удален");

        return new SuccessfulResponse([
            'user_delete' =>  "Пользователь с id: {$user->getId()} удален.", 
        ]);
    }
}
