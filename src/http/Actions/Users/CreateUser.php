<?

namespace GummerD\PHPnew\http\Actions\Users;

use Faker\Factory;
use Psr\Log\LoggerInterface;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserAlradyExistsException;


class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $userRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $facker = Factory::create('ru_Ru');

        try {
            $user_id = UUID::random();

            $user = new User(
                $user_id,
                $request->jsonBodyField('username'),
                new Name(
                    $facker->firstName(),
                    $facker->lastName()
                )
            );
  
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // ввел новое исключение + логирование ошибки
        try {
            $this->userRepository->save($user); 
        } catch (UserAlradyExistsException $e) {
            $this->logger->warning("
                Неудачная попытка создать пользователя 
                с существующим логином:{$user->getUsername()}"
            );
            return new ErrorResponse($e->getMessage());
        }
        
        // ввел логирование на нового пользователя
        $this->logger->info("Создан пользователь с логином:{$user->getUsername()}");

        return new SuccessfulResponse(
            [
                'save_new_user' => "Новый пользователь: {$user->getUsername()} сохранен"
            ]
        );
    }
}
