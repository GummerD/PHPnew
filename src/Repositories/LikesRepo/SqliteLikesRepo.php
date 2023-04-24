<?

namespace GummerD\PHPnew\Repositories\LikesRepo;

use PDO;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\Likes\FounLikeException;
use GummerD\PHPnew\Exceptions\Likes\LikesNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;

class SqliteLikesRepo implements LikesRepositoryInterface
{
    public function __construct(
        protected PDO $connection,
    ) {
    }

    public function save(Likes $likes): void
    {
        $like_id = $likes->getLikeId();
        $post_id = $likes->getPostId()->getId();
        $owner_id = $likes->getOwnerId()->getId();


        $statement = $this->connection->prepare(
            "INSERT INTO likes (like_id, id_for_post, id_for_user) 
                VALUES (:like_id, :post_id, :owner_id)
            "
        );

        $statement->execute([
            ':like_id' => $like_id,
            ':post_id' => $post_id,
            ':owner_id' => $owner_id
        ]);

        echo "Лайк добавлен в БД";
    }

    public function getLikesById($id): Likes
    {
        $id = new UUID($id);

        $statement = $this->connection->prepare(
            "SELECT * FROM likes 
                LEFT JOIN posts
                    ON likes.id_for_post = posts.post_id
                LEFT JOIN users
                    ON likes.id_for_user = users.user_id
                WHERE like_id = :id
            "
        );

        $statement->execute([
            ':id' => $id
        ]);

        return $this->getResult($statement, 'id', $id);
    }

    public function getLikesByOwnerId($id): Likes
    {
        $id = new UUID($id);

        $statement = $this->connection->prepare(
            "SELECT * FROM likes 
                LEFT JOIN posts
                    ON likes.post_id = posts.post_id
                LEFT JOIN users
                    ON likes.owner_id = users.user_id
                WHERE owner_id = :user_id
            "
        );

        $statement->execute([
            ':user_id' => $id
        ]);

        return $this->getResult($statement, 'owner_id', $id);
    }

    public function getLikesByPostId($id): Likes
    {
        $id = new UUID($id);

        $statement = $this->connection->prepare(
            "SELECT * FROM likes 
                LEFT JOIN posts
                    ON likes.id_for_post = posts.post_id
                LEFT JOIN users
                    ON likes.id_for_user = users.user_id
                WHERE id_for_post = :post_id
            "
        );

        $statement->execute([
            ':post_id' => $id
        ]);

        $countPost = $statement->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($countPost);

        return $this->getResult($statement, 'post_id', $id);
    }

    public function getResult(\PDOStatement $statement, $name, $variables): Likes
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        //var_dump($result);
        
        if ($result === false) {
            throw new LikesNotFoundException("
                Лайка с таким {$name} {$variables} не существует 
            ");
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

        return new Likes(
            new UUID($result['like_id']),
            $post,
            $user
        );
    }

    public function CheckOwnerInTablelikes(Post $post, User $owner): bool
    {   
        $post_id = $post->getId();
        $owber_id = $owner->getId();
        
        $statement = $this->connection->prepare(
            "SELECT * FROM likes
                LEFT JOIN posts
                    ON likes.id_for_post = posts.post_id
                LEFT JOIN users
                    ON likes.id_for_user = users.user_id
                WHERE id_for_user = :owner_id AND id_for_post = :post_id
        "
        );

        $statement->execute([
            ':post_id' => (string)$post_id,
            ':owner_id' => (string)$owber_id
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result !== false){
            //var_dump($result);
            return false;
        }

        return true;
    }

    public function delete($like_id): void
    {
        $statement = $this->connection->prepare(
            "DELETE FROM likes WHERE like_id=:like_id"
        );

        $statement->execute([
            ':like_id' => $like_id
        ]);

        echo "Like удален.";
    }

    public function getAllLikesForPost($post_id): int
    {   
        $statement = $this->connection->prepare(
            "SELECT * FROM likes 
                LEFT JOIN posts
                    ON likes.id_for_post = posts.post_id
                LEFT JOIN users
                    ON likes.id_for_user = users.user_id
                WHERE id_for_post = :post_id
            "
        );

        $statement->execute([
            ':post_id' => $post_id
        ]);

        $countPost = $statement->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($countPost);

        $posts = [];
        foreach($countPost as $post => $value){
            $posts[] = $value["id_for_post"];
            //var_dump($posts);
        }

        $countPosts =  count($posts);
        return $countPosts;
    }
}
