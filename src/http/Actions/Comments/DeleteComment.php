<?

namespace GummerD\PHPnew\http\Actions\Comments;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Exceptions\CommentsExceptions\CommentNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $comment_id = $request->query('comment_id');
            $this->commentsRepository->getCommentById($comment_id);
        } catch (HttpException | CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->commentsRepository->delete($comment_id);
        } catch (CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info("Комментарий с id:{$comment_id} удаленю");

        return new SuccessfulResponse([
            'user_delete' =>  "Комментарий с id: {$comment_id} удален.", 
        ]);
    }
}
