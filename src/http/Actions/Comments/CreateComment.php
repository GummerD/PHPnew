<?

namespace GummerD\PHPnew\http\Actions\Comments;

use Faker\Factory;
use Psr\Log\LoggerInterface;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\Authentication\TokenAuthenticationInterface;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;


class CreateComment implements ActionInterface
{

    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private CommentsRepositoriesInterface $commentsRepository,
        private TokenAuthenticationInterface $identification,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $facker = Factory::create('ru_Ru');

        try {
            $postId = new UUID($request->jsonBodyField('post_id'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {  
            $post = $this->postsRepository->getPostById($postId);
        } catch (UserNotFoundException | PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $author = $this->identification->user($request);

        $newCommentId = UUID::random();

        try {
            $comment = new Comment(
                $newCommentId,
                $author,
                $post,
                $facker->text(30)
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsRepository->save($comment);

        $this->logger->info("Пользователем под логином {$author->getUsername()} создан новый комментарий с id: {$comment->getId()}, к статье с id: {$comment->getPostId()->getId()} через SqliteCommentsRepo");

        return new SuccessfulResponse([
            'save_new_comment' => "Новый комментарий, id: {$newCommentId} сохранен.",
            'owner' => "Создатель: {$author->getUsername()}."
        ]);
    }
}
