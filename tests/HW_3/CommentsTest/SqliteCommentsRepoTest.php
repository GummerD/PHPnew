<?

namespace GummerD\PHPnew\UnitTests\HW_3\CommentsTest;

use PHPUnit\Framework\TestCase;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Exceptions\CommentsExceptions\CommentNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;
use GummerD\PHPnew\Models\UUID;

/**
 * Summary of SqliteCommentsRepoTest
 */
class SqliteCommentsRepoTest extends TestCase
{
    /**
     * Summary of makeCommentRepositoryMock
     * @return object
     * Функция для создания мока под хранилеще сообщений
     */
    public function makeCommentRepositoryMock()
    {
        // сздаем анонимный класс реализуя интерфейс CommentsRepository
        return new class implements CommentsRepositoriesInterface
        {
            private bool $called = false;

            public function save(Comment $post): void
            {
                $this->called = true;
            }

            public function getAllComments(): void
            {
                $this->called = true;
            }

            public function getCommentById($id): Comment
            {
                if ($id == 'd7acbea9-7cda-4374-8886-25a18b29d1b4') {

                    $this->called = true;

                    return new Comment(
                        new UUID('d7acbea9-7cda-4374-8886-25a18b29d1b4'),
                        UUID::random(),
                        UUID::random(),
                        'some_text'
                    );
                } else {
                    throw new CommentNotFoundException(
                        "Комментария с таким id:{$id} нет в БД"
                    );
                }
            }

            public function getCommentByOwner_id($owner_id): Comment
            {
                if ($owner_id == 'd7acbea9-7cda-4374-8886-25a18b29d1b3') {

                    $this->called = true;

                    return new Comment(
                        new UUID('d7acbea9-7cda-4374-8886-25a18b29d1b3'),
                        UUID::random(),
                        UUID::random(),
                        'some_text'
                    );
                } else {
                    throw new CommentNotFoundException(
                        "Комментария с таким id пользователя: {$owner_id}, нет в БД"
                    );
                }
            }

            public function returnCalled(): bool
            {
                return $this->called;
            }
        };
    }

    public function testItSavesCommentInRepository()
    {
        $comment = new Comment(
            UUID::random(),
            UUID::random(),
            UUID::random(),
            'some_text'
        );

        $commentRepositoryMock = $this->makeCommentRepositoryMock();

        $commentRepositoryMock->save($comment);

        $this->assertTrue($commentRepositoryMock->returnCalled());
    }

    public function testItGetAllComments()
    {
        $commentRepositoryMock = $this->makeCommentRepositoryMock();

        $commentRepositoryMock->getAllComments();

        $this->assertTrue($commentRepositoryMock->returnCalled());
    }

    public function testItFindCommentById()
    {
        $id = 'd7acbea9-7cda-4374-8886-25a18b29d1b4';

        $commentRepositoryMock = $this->makeCommentRepositoryMock();

        $commentRepositoryMock->getCommentById($id);

        $this->assertTrue($commentRepositoryMock->returnCalled());
    }

    public function testItThrowAnExceptionWhenCommentByIdNotFound()
    {
        $id = 'd7acbea9-7cda-4374-8886-25a18b29d1b5';

        $commentRepositoryMock = $this->makeCommentRepositoryMock();

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Комментария с таким id:{$id} нет в БД");

        $commentRepositoryMock->getCommentById($id);
    }

    public function testItFindCommentByOwnerId()
    {
        $owner_id = 'd7acbea9-7cda-4374-8886-25a18b29d1b3';

        $commentRepositoryMock = $this->makeCommentRepositoryMock();

        $commentRepositoryMock->getCommentByOwner_id($owner_id);

        $this->assertTrue($commentRepositoryMock->returnCalled());
    }

    public function testItThrowAnExceptionWhenCommentByOwnerId()
    {
        $owner_id = 'd7acbea9-7cda-4374-8886-25a18b29d1b4';

        $commentRepositoryMock = $this->makeCommentRepositoryMock();

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Комментария с таким id пользователя: {$owner_id}, нет в БД");

        $commentRepositoryMock->getCommentByOwner_id($owner_id);
    }
}
