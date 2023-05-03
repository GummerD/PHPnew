<?

namespace GummerD\PHPnew\Repositories\LikesRepo;

use PDO;
use Psr\Log\LoggerInterface;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\Models\Person\Name;
use GummerD\PHPnew\Exceptions\Likes\LikesNotFoundException;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;

class SqliteLikesRepo implements LikesRepositoryInterface
{
    public function __construct(
        protected PDO $connection,
        private LoggerInterface $logger
    ) {
    }

    public function save(Likes $likes): void
    {
        try {
            $like_id = new UUID($likes->getLikeId());
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikesRepo id-лайка для сохранения указан неверно.");
            $e->getMessage();
        }

        try {
            $post_id = new UUID($likes->getPostId()->getId());
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikesRepo id-поста для сохранения указан неверно.");
            $e->getMessage();
        }

        try {
            $owner_id = new UUID($likes->getOwnerId()->getId());
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikesRepo id-пользователя для сохранения указан неверно.");
            $e->getMessage();
        }


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

    }

    public function getLikesById($id): Likes
    {   

        try {
            $id = new UUID($id);
            $this->logger->info("Иницилизирован поиск лайка с id: {$id} через SqliteLikessRepo");  
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikessRepo id: {$id} указан неверно.");
            $e->getMessage();
        }

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
        try {
            $id = new UUID($id);
            $this->logger->info("Иницилизирован поиск лайков у пользователя с id: {$id} через SqliteLikessRepo");
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikessRepo id: {$id} указан неверно.");
            $e->getMessage();
        }

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
        try {
            $id = new UUID($id);
            $this->logger->info("Иницилизирован поиск лайков у статьи с id: {$id} через SqliteLikessRepo");
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikessRepo id: {$id} указан неверно.");
            $e->getMessage();
        }

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
            $this->logger->warning("Запрос через SqliteLikesRepo для реакции(лайка) с {$name}:{$variables} не выполнен.");
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

        $this->logger->info(
            "Реакция с id {$result['like_id']} найдена и передана в свой action."
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

        if ($result !== false) {
            //var_dump($result);
            return false;
        }

        return true;
    }

    public function delete($like_id): void
    {
        try {
            $like_id = new UUID($like_id);
            $this->logger->info("Инициализирован запрос на удаление реакции(лайка) с id: {$like_id} через SqliteLikesRepo");
        } catch (InvalidArgumentException $e) {
            $this->logger->warning("Направленный в SqliteLikesRepo на удаление пользователь с id: {$like_id} указан неверно.");
            $e->getMessage();
        }

        $statement = $this->connection->prepare(
            "DELETE FROM likes WHERE like_id=:like_id"
        );

        $statement->execute([
            ':like_id' => $like_id
        ]);
    }

    public function getAllLikesForPost($post_id): int
    {   
        $this->logger->info("Инициализирован запрос на получение всех реакций(лайков) для поста с id: {$post_id} через SqliteLikesRepo");

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

        $posts = [];

        foreach ($countPost as $post => $value) {
            $posts[] = $value["id_for_post"];
        }

        $countPosts = count($posts);

        $this->logger->info("Поиск всех лайков (количество {$countPosts}) для поста с id: {$post_id} через SqliteLikesRepo исполнено");

        return $countPosts;
    }
}
