<?

namespace GummerD\PHPnew\UnitTests\UserTest\CommentsTest;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use PHPUnit\Framework\TestCase;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

/**
 * Summary of SqliteCommentsRepoTests
 */
class SqliteUsersRepoTest extends TestCase
{
    /**
     * Summary of makeCommentRepositoryMock
     * @return object
     * Функция для создания мока под хранилеще сообщений
     */
    public function makeUserRepositoryMock()
    {
        // сздаем анонимный класс реализуя интерфейс CommentsRepository
        return new class implements UsersRepositoryInterface
        {
            private bool $called = false;

            public function save(User $post): void
            {
                $this->called = true;

            }

            public function getByUserId($id): User
            {
                if ($id === '12345') {

                    $this->called = true;

                    return new User(
                        UUID::random(),
                        'some_username',
                        'some_passwoed',
                        new Name('first_name', 'last_name')
                    );
                } else {
                    throw new UserNotFoundException(
                        "Пользователя с таким id:{$id} нет в БД"
                    );
                }
                
            }

            public function getByUsername($username): User
            {
                if ($username === 'some_username') {

                    $this->called = true;

                    return new User(
                        UUID::random(),
                        'some_username',
                        'some_passwoed',
                        new Name('first_name', 'last_name')
                    );
        
                } else {
                    throw new UserNotFoundException(
                        "Пользователя с таким логином:{$username} нет в БД"
                    );
                }
            }


            public function delete($id): void
            {
                $this->called = true;
            }

            public function returnCalled(): bool
            {
                return $this->called;
            }
        };
    }

    public function testItSaveUserInRepository()
    {

        $user = new User(
            UUID::random(),
            'some_username',
            'some_passwoed',
            new Name('first_name', 'last_name')
        );

        $userRepositoryMock = $this->makeUserRepositoryMock();

        $userRepositoryMock->save($user);

        $this->assertTrue($userRepositoryMock->returnCalled());
    }

    public function testItFindUserByUsername()
    {
        $username ='some_username';

        $userRepositoryMock = $this->makeUserRepositoryMock();

        $userRepositoryMock->getByUsername($username);

        $this->assertTrue($userRepositoryMock->returnCalled());
    }

    public function testItThrowAnExceptionWhenUserByIdNotFound()
    {
        $id ='123';

        $userRepositoryMock = $this->makeUserRepositoryMock();

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Пользователя с таким id:{$id} нет в БД");

        $userRepositoryMock->getByUserId($id);
    }
}
