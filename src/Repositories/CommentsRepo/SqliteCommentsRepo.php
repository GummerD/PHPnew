<?

namespace GummerD\PHPnew\Repositories\CommentsRepo;


use PDO;
use PDOStatement;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
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
        //print_r($comment);

        $statement = $this->connection->prepare(
            "INSERT INTO comments (comment_id, owner_id, post_id, text) 
                VALUES (:comment_id, :owner_id, :post_id, :text)
            "
        );
    
        $statement->execute([
            ':comment_id' => $comment->getId(),
            ':owner_id' => $comment->getOwnerId()->getId(),
            ':post_id' => $comment->getPostId()->getId(),
            ':text' => $comment->getText()
        ]);

        echo "Комментарий сохранен. <br>";
    }
    /**
     * Summary of getAll
     * @return Comment
     */
    public function getAllComments(): void
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comments
                LEFT JOIN users
                    ON comments.owner_id = users.user_id
                LEFT JOIN posts
                    ON comments.post_id = posts.post_id
                "
        );

        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        print_r($results);

        foreach ($results as $result) {
            echo new Comment(
                new UUID($result['comment_id']),
                new User(
                    new UUID($result['owner_id']),
                    $result['username'],
                    new Name($result['first_name'], $result['last_name'])
                ),
                new Post(
                    new UUID($result['post_id']),
                    new User(
                        new UUID($result['owner_id']),
                        $result['username'],
                        new Name($result['first_name'], $result['last_name'])
                    ),
                    $result['title'],
                    $result['text']
                ),
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
        try {
            $id = new UUID($id);
        } catch (InvalidArgumentException $e) {
            $e->getMessage();
        }

        $statement = $this->connection->prepare(
            "SELECT * FROM comments
                LEFT JOIN users
                    ON comments.owner_id = users.user_id
                LEFT JOIN posts
                    ON comments.post_id = posts.post_id
                WHERE comment_id = :comment_id
            "
        );

        $statement->execute([
            ':comment_id' => $id
        ]);

        
        return $this->getResult($statement, 'comment_id', $id);
    }
    /**
     * Summary of getPostById
     * @param string $owner_id
     * @return Comment
     */
    public function getCommentByOwner_id($owner_id): Comment
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comments
                LEFT JOIN users
                    ON comments.owner_id = users.user_id
                LEFT JOIN posts
                    ON comments.post_id = posts.post_id 
                    WHERE owner_id = :owner_id
                "
        );

        $statement->execute([
            ':owner_id' => $owner_id
        ]);

        return $this->getResult($statement, 'пользователем', $owner_id);
    }

    public function getResult(PDOStatement $statement, $name, $variable): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(
                "Комментария с таким {$name}:{$variable} нет в БД"
            );
        }

        $user = new User(
            new UUID($result['owner_id']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );

        $post = new Post(
            new UUID($result['post_id']),
            $user,
            $result['title'],
            $result['text']
        );

        return new Comment(
            new UUID($result['comment_id']),
            $user,
            $post,
            $result['text']
        );
    }

    public function delete($id): void
    {   
        $id = new UUID($id);
        
        $statement = $this->connection->prepare(
            "DELETE FROM comments WHERE comment_id = :id" 
        );

        $statement->execute([
            ':id'=>(string)$id,
        ]);
    }
}
