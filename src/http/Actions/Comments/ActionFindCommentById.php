<?

namespace GummerD\PHPnew\http\Actions\Comments;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\CommentsExceptions\CommentNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class ActionFindCommentById implements ActionInterface
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
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = $this->commentsRepository->getCommentById($comment_id);
        } catch (CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info("Информация о комментарии с id:{$comment_id} получена.");

        return new SuccessfulResponse([
            'comment_id' =>  $comment->getId()->uuidString(), 
            'owner_name' => 'Создатель комментария:' . $comment->getOwnerId()->getName()->getFirstname() . ' ' . $comment->getOwnerId()->getName()->getLastname(),
        ]);
    }
}
