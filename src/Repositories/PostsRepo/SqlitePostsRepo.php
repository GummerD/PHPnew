<?
namespace GummerD\PHPnew\Repositories\PostsRepo;

use PDO;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;

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
    ){}

    /**
     * Summary of save
     * @param Post $post
     * @return void
     */
    public function save(Post $post):void
    {
        //print_r($post);
        $statement = $this->connection->prepare(
            "INSERT INTO posts (id, owner_id, title, text) 
                VALUES (:id, :owner_id, :title, :text)"
        );

        $statement->execute([
            ':id' => $post->getId(), 
            ':owner_id' => $post->getOwnerId(), 
            ':title' =>$post->getTitle(), 
            ':text' => $post->getText()
        ]);
    }

    /**
     * Summary of getAllPosts
     * @return void
     */
    public function getAllPosts():void
    { 
        $statement = $this->connection->prepare(
            "SELECT * FROM posts"
        );
        
        $statement->execute();
        
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        //print_r($results);
        
        foreach( $results as $result){
           echo new Post(
                new UUID($result['id']),
                new UUID($result['owner_id']),
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
    public function getPostById($id):Post
    {
        print_r($id);
        $statement = $this->connection->prepare(
            "SELECT * FROM posts WHERE id = :id"
        );

        print_r($statement);

        $statement->execute([
            ":id" => (string)$id
        ]);

        print_r($statement);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new PostNotFoundException(
                "Поста с таким id:{$id} не существует."
            );
        }

        return $this->getResult($result);
    }

    /**
     * Summary of getPostByTitlt
     * @param mixed $title
     * @return Post
     */
    public function getPostByTitle($title):Post
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM posts WHERE title = :title"
        );

        $statement->execute([
            ':title' => $title
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new PostNotFoundException(
                "Поста с таким заголовком:{$title} не существует."
            );
        }

        return $this->getResult($result);
    }


    public function getResult($result):Post
    {
        return new Post(
            new UUID($result['id']),
            new UUID($result['owner_id']),
            $result['title'],
            $result['text']
        );  
    }
}