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
use Psr\Log\LoggerInterface;

class SqliteCommentsRepo implements CommentsRepositoriesInterface
{

    /**
     * Summary of __construct
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Summary of save
     * @param Comment $post
     * @return void
     */
    public function save(Comment $comment): void
    {
        $this->logger->info("Иницилизировано создание нового комментария с id: {$comment->getId()} к посту {$comment->getPostId()->getId()} через SqliteCommentsRepo");

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

    }
    /**
     * Summary of getAll
     * @return Comment
     */
    public function getAllComments(): void
    {   
        $this->logger->info("Иницилизирован поиск всех комментариев в БД через SqliteCommentsRepo");

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
            $this->logger->info("Иницилизирован поиск комментария с id {$id} через SqliteCommentsRepo");
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Поиск комментария с id {$id} через SqliteCommentsRepo потерпел недачу");
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
        try {
            $owner_id = new UUID($owner_id);
            $this->logger->info("Иницилизирован поиск комментария по id ползователя {$owner_id} через SqliteCommentsRepo");
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Поиск комментария о id ползователя {$owner_id} через SqliteCommentsRepo потерпел недачу");
            $e->getMessage();
        }


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
            ':owner_id' => (string)$owner_id
        ]);

        return $this->getResult($statement, 'пользователем', $owner_id);
    }

    public function getResult(PDOStatement $statement, $name, $variable): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Поиск комментария с таким {$name}:{$variable} через SqliteCommentsRepo потерпел недачу");
            throw new CommentNotFoundException(
                "Комментария с таким {$name}:{$variable} нет в БД"
            );

            return false;
        }

        $user = new User(
            new UUID($result['owner_id']),
            $result['username'],
            $result['password'],
            new Name($result['first_name'], $result['last_name'])
        );

        $post = new Post(
            new UUID($result['post_id']),
            $user,
            $result['title'],
            $result['text']
        );

        $this->logger->warning("Результат поиска для {$name} с :{$variable}  направлен в action.");

        return new Comment(
            new UUID($result['comment_id']),
            $user,
            $post,
            $result['text']
        );
    }

    public function delete($id): void
    {   
        
        try {
            $id = new UUID($id);
            $this->logger->info("Иницилизировано удаление комменатрия с id:{$id} через SqliteCommentsRepo");
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Удаление комменатрия с id:{$id} через SqliteCommentsRepo потерпело недачу");
            $e->getMessage();
        }
        
        $statement = $this->connection->prepare(
            "DELETE FROM comments WHERE comment_id = :id" 
        );

        $statement->execute([
            ':id'=>(string)$id,
        ]);
    }
}
