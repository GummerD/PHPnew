<?

namespace GummerD\PHPnew\UnitTests\Commands;


use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use PHPUnit\Framework\TestCase;
use GummerD\PHPnew\Commands\Users\UserCreate;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class UserCreateCommandTest extends TestCase
{
    private function makeUsersRepository(): UsersRepositoryInterface
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

            public function UserExists($username): bool
            {
                if ($username !== 'Ivan') {
                    throw new UserNotFoundException("Пользователя с таким логином:{$username} не существует.");
                }

                return true;
            }

            public function delete($id): void
            {
                echo "Пользоватлеь с $id удален";
            }
        };
    }


    public function testItRequiresLastName(): void
    {
        $command = new UserCreate(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );


        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',

            ]),
            new NullOutput()
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new UserCreate(
            $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name, password"'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresFirstName(): void
    {
        $command = new UserCreate(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface
        {
            private bool $called = false;

            public function save(User $user): void
            {
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

            public function wasCalled(): bool
            {
                return $this->called;
            }

            public function UserExists($username): bool
            {
                return true;
            }

            public function delete($id): void
            {
            }
        };

        $command = new UserCreate(
            $usersRepository
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }
}
