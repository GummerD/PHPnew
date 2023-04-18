<?

namespace GummerD\PHPnew\Repositories\PostsRepo;

use PDO;
use PDOStatement;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use InvalidArgumentException;

/**
 * Summary of SqlitePostsRepo
 */
class SqlitePostsRepo implements PostsRepositoriesInterface
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
     * @param Post $post
     * @return void
     */
    public function save(Post $post): void
    {
        //print_r($post);
        $statement = $this->connection->prepare(
            "INSERT INTO posts (post_id, owner_id, title, text) 
                VALUES (:post_id, :owner_id, :title, :text)
            "
        );

        $statement->execute([
            ':post_id' => $post->getId(),
            ':owner_id' => $post->getUser()->getId(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()
        ]);

        echo "Пост сохранен";
    }

    /**
     * Summary of getAllPosts
     * @return void
     */
    public function getAllPosts(): void
    {
        $statement = $this->connection->prepare(
            "SELECT * 
             FROM posts LEFT JOIN users
                ON posts.owner_id = users.user_id
            "
        );

        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        //print_r($results);

        foreach ($results as $result) {
            echo new Post(
                new UUID($result['post_id']),
                new User(
                    new UUID($result['owner_id']),
                    $result['username'],
                    new Name($result['first_name'], $result['last_name'])
                ),
                $result['title'],
                $result['text']
            );
        }
    }

    /**
     * Summary of getPostById
     * @param mixed $id
     * @return Post
     */
    public function getPostById($post_id): Post
    {   
        try {
            $post_id = new UUID($post_id);
        } catch (InvalidArgumentException $e) {
            $e->getMessage();
        }
        
        
        $statement = $this->connection->prepare(
            "SELECT * 
             FROM posts LEFT JOIN users
                ON posts.owner_id = users.user_id
                WHERE posts.post_id =:post_id
            "
        );

        $statement->execute([
            ":post_id" => (string)$post_id
        ]);

        return $this->getResult($statement, 'id', $post_id);
    }

    /**
     * Summary of getPostByTitlt
     * @param mixed $title
     * @return Post
     */
    public function getPostByTitle($title): Post
    {
        $statement = $this->connection->prepare(
            "SELECT * 
             FROM posts LEFT JOIN users
                ON posts.owner_id = users.user_id
                WHERE posts.title =:title
            "
        );

        $statement->execute([
            ':title' => $title
        ]);

        return $this->getResult($statement, 'заголовком', $title);
    }
    public function getResult(PDOStatement $statement, $name, $variable): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        //print_r($result);
        if ($result === false) {
            throw new PostNotFoundException(
                "Поста с таким {$name}:{$variable} не существует."
            );
        }

        $user = new User(
            new UUID($result['owner_id']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );

        return new Post(
            new UUID($result['post_id']),
            $user,
            $result['title'],
            $result['text']
        );
    }
    public function delete($id): void
    {   
        $id = new UUID($id);
        
        $statement = $this->connection->prepare(
            "DELETE FROM posts WHERE post_id = :id" 
        );

        $statement->execute([
            ':id'=>(string)$id,
        ]);
    }
}
