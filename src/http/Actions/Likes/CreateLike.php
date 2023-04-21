<?

namespace GummerD\PHPnew\http\Actions\Likes;

use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\Likes\FounLikeException;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class CreateLike implements ActionInterface
{
    public function __construct(
        protected LikesRepositoryInterface $likesRepository,
        protected UsersRepositoryInterface $usersRepository,
        protected PostsRepositoriesInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $owner= $request->jsonBodyField('owner_id');
            $post= $request->jsonBodyField('post_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $owner = $this->usersRepository->getByUserId($owner);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }


        try {
            $post = $this->postsRepository->getPostById($post);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $checkTableLikes = $this->likesRepository->CheckOwnerInTablelikes($post, $owner);

        try {
            if ($checkTableLikes === false) {
                throw new FounLikeException("
                    Пользователь с id: {$owner->getId()}, уже ставил лайк этой статье {$post->getId()}.
                ");
            }
        } catch (FounLikeException $e) {
            return new ErrorResponse($e->getMessage());
        }


        try {
            $like = new Likes(
                UUID::random(),
                $post,
                $owner
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'create_like' => "Получена новая реакция ID: {$like->getLikeId()}",
            'post_id' => "На пост с ID: {$post->getId()}",
            'user' => "От пользователя c логином {$owner->getUsername()}"
        ]);
    }
}
