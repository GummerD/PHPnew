<?

namespace GummerD\PHPnew\UnitTests\HW_3\PostsTest;

use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use PHPUnit\Framework\TestCase;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use PDO;
use PDOStatement;

/**
 * Summary of SqliteCommentsRepoTest
 */
class SqlitePostsRepoTest extends TestCase
{
    /**
     * Summary of makeCommentRepositoryMock
     * @return object
     * Функция для создания мока под хранилеще постов
     */
    public function makePostRepositoryMock()
    {
        // сздаем анонимный класс реализуя интерфейс PostsRepository
        return new class implements PostsRepositoriesInterface
        {
            private bool $called = false;

            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function getAllPosts(): void
            {
                $this->called = true;
            }

            public function getPostById($id): Post
            {
                if ($id == 'd7acbea9-7cda-4374-8886-25a18b29d1b4') {

                    $this->called = true;

                    return new Post(
                        new UUID('d7acbea9-7cda-4374-8886-25a18b29d1b4'),
                        new User(
                            UUID::random(),
                            'some_username',
                            new Name('some_firstname', 'some_lastname')
                        ),
                        'some_title',
                        'some_text'
                    );

                } else {
                    throw new PostNotFoundException(
                        "Поста с таким id:{$id} не существует."
                    );
                }
            }

            public function getPostByTitle($title): Post
            {
                if ($title == 'some_title') {

                    $this->called = true;

                    return new Post(
                        UUID::random(),
                         new User(
                            UUID::random(),
                            'some_username',
                            new Name('some_firstname', 'some_lastname')
                        ),
                        'some_title',
                        'some_text'
                    );
                } else {
                    throw new PostNotFoundException(
                        "Поста с таким заголовком:{$title} не существует."
                    );
                }
            }

            public function returnCalled(): bool
            {
                return $this->called;
            }
        };
    }

    public function testItSavesPostInRepository()
    {
        $post = new Post(
            new UUID('d7acbea9-7cda-4374-8886-25a18b29d1b4'),
            new User(
                UUID::random(),
                'some_username',
                new Name('some_firstname', 'some_lastname')
            ),
            'some_title',
            'some_text'
        );

        $postRepositoryMock = $this->makePostRepositoryMock();

        $postRepositoryMock->save($post);

        $this->assertTrue($postRepositoryMock->returnCalled());
    }

    public function testItGetAllPosts()
    {
        $postRepositoryMock = $this->makePostRepositoryMock();

        $postRepositoryMock->getAllPosts();

        $this->assertTrue($postRepositoryMock->returnCalled());
    }

    public function testItFindPostById()
    {
        $id = 'd7acbea9-7cda-4374-8886-25a18b29d1b4';

        $postRepositoryMock = $this->makePostRepositoryMock();

        $postRepositoryMock->getPostById($id);

        $this->assertTrue($postRepositoryMock->returnCalled());
    }

    public function testItThrowAnExceptionWhenPostByIdNotFound()
    {
        $id = 'd7acbea9-7cda-4374-8886-25a18b29d1b5';

        $postRepositoryMock = $this->makePostRepositoryMock();

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Поста с таким id:{$id} не существует.");

        $postRepositoryMock->getPostById($id);
    }

    public function testItFindPostByTitle()
    {
        $title = 'some_title';

        $postRepositoryMock = $this->makePostRepositoryMock();

        $postRepositoryMock->getPostByTitle($title);

        $this->assertTrue($postRepositoryMock->returnCalled());
    }

    public function testItThrowAnExceptionWhenCommentByOwnerId()
    {
        $title = 'error_title';

        $postRepositoryMock = $this->makePostRepositoryMock();

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Поста с таким заголовком:{$title} не существует.");

        $postRepositoryMock->getPostByTitle($title);
    }
}
