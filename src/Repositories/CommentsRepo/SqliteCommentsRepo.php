<?

namespace GummerD\PHPnew\Repositories\CommentsRepo;


use PDO;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Exceptions\CommentsExceptions\CommentNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;

class SqliteCommentsRepo implements CommentsRepositoriesInterface
{

    /**
     * Summary of __construct
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection
    ) {
    }

    /**
     * Summary of save
     * @param Comment $post
     * @return void
     */
    public function save(Comment $comment): void
    {
        print_r($comment);

        $statement = $this->connection->prepare(
            "INSERT INTO comments (id, owner_id, post_id, text) 
                VALUES (:id, :owner_id, :post_id, :text)
            "
        );

        $statement->execute([
            ':id' => $comment->getId(),
            ':owner_id' => $comment->getOwnerId(),
            ':post_id' => $comment->getPostId(),
            ':text' => $comment->getText()
        ]);
    }
    /**
     * Summary of getAll
     * @return Comment
     */
    public function getAllComments(): void
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comments"
        );

        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            echo new Comment(
                $result['id'],
                $result['owner_id'],
                $result['post_id'],
                $result['text']
            );
        }
    }
    /**
     * Summary of getPostById
     * @param string $id
     * @return Comment
     */
    public function getCommentById($id): Comment
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comments WHERE id = :id"
        );

        $statement->execute([
            ':id' => $id
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(
                "Комментария с таким id:{$id} нет в БД"
            );
        }
        return $this->getResult($result);
    }
    /**
     * Summary of getPostById
     * @param string $owner_id
     * @return Comment
     */
    public function getCommentByOwner_id($owner_id): Comment
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comments WHERE owner_id = :owner_id"
        );

        $statement->execute([
            ':owner_id' => $owner_id
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Комментария с таким id пользователя: {$owner_id}, нет в БД"
            );
        }

        return $this->getResult($result);
    }

    public function getResult($result): Comment
    {
        return new Comment(
            $result['id'],
            $result['owner_id'],
            $result['post_id'],
            $result['text']
        );
    }
}
