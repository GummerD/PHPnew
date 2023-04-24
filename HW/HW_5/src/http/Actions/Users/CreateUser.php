<?

namespace GummerD\PHPnew\http\Actions\Users;

use Faker\Factory;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\Exceptions\http\UserFoundException;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $userRepository
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

            $this->userRepository->save($user);
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }


        return new SuccessfulResponse(
            [
                'save_new_user' => "Новый пользователь: {$user->getUsername()} сохранен"
            ]
        );
    }
}
