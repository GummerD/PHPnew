<?

namespace GummerD\PHPnew\UnitTests\Commands;

use GummerD\PHPnew\Exceptions\Arguments\ArgumentsExceptions;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use PHPUnit\Framework\TestCase;
use GummerD\PHPnew\Commands\Users\Arguments;
use GummerD\PHPnew\Commands\Users\CreateUsersCommands;
use GummerD\PHPnew\Exceptions\CommadsExceptions\CommandException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Repositories\UserRepo\ForTest\DummyUserRepository;
use GummerD\PHPnew\UnitTests\Dummy\Logger\DummyLogger;

/**
 * Summary of CreateUserCommandTest
 */
class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUsersCommands(new DummyUserRepository(),new DummyLogger());

        $this->expectException(CommandException::class);

        $this->expectExceptionMessage('Пользователь с таким Ivan логиномы уже существует');

        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    /**
     * Summary of makeUserRepository
     * @return UsersRepositoryInterface
     */
    private function makeUserRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface
        {
            public function save(User $user): void
            {
            }
            public function getByUserId($id): User
            {
                $id = new UUID($id);

                throw new UserNotFoundException("Пользователя с таким id:{$id} не существует");
            }
            public function getByUsername($username): User
            {
                throw new UserNotFoundException("Пользователя с таким логином:{$username} не существует.");
            }

            public function delete($id): void
            {

            }
        };
    }

    /**
     * Summary of testItRequiresLastName
     * @return void
     */
    public function testItRequiresLastName(): void
    {
        $username = [
            'username' => 'Ivan',
            'first_name' => 'Ivan'
        ];

        $command = new CreateUsersCommands($this->makeUserRepository(), new DummyLogger());

        $this->expectException(ArgumentsExceptions::class);

        $this->expectExceptionMessage("Значение не найдено: last_name");

        $command->handle(new Arguments($username));
    }

    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface
        {

            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function getByUserId($id): User
            {   
                $id = new UUID($id);

                throw new UserNotFoundException("Пользователя с таким id:{$id} не существует");
            }

            public function getByUsername($username): User
            {
                throw new UserNotFoundException("Пользователя с таким логином:{$username} не существует.");
            }

            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }

            public function delete($id): void
            {

            }
        };

        // Передаём наш мок в команду
        $command = new CreateUsersCommands($usersRepository, new DummyLogger());

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }
}
