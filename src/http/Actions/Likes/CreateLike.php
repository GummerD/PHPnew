<?

namespace GummerD\PHPnew\http\Actions\Likes;

use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\Exceptions\Likes\FounLikeException;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\http\Identification\JsonBodyIdentificationUserByUsername;
use Psr\Log\LoggerInterface;

class CreateLike implements ActionInterface
{
    public function __construct(
        protected LikesRepositoryInterface $likesRepository,
        private JsonBodyIdentificationUserByUsername $identification,
        protected PostsRepositoriesInterface $postsRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $post= $request->jsonBodyField('post_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $author = $this->identification->user($request);

        try {
            $post = $this->postsRepository->getPostById($post);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $checkTableLikes = $this->likesRepository->CheckOwnerInTablelikes($post, $author);

        try {
            if ($checkTableLikes === false) {
                throw new FounLikeException("
                    Пользователь с id: {$author->getId()}, уже ставил лайк этой статье id: {$post->getId()}.
                ");
            }
        } catch (FounLikeException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $like = new Likes(
                UUID::random(),
                $post,
                $author
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesRepository->save($like);

        $this->logger->info(
            "Пользователь {$author->getUsername()} 
            поставил лайк статье с ID: {$post->getId()}"
        );

        return new SuccessfulResponse([
            'create_like' => "Получена новая реакция ID: {$like->getLikeId()}",
            'post_id' => "На пост с ID: {$post->getId()}",
            'user' => "От пользователя c логином {$author->getUsername()}"
        ]);
    }
}
