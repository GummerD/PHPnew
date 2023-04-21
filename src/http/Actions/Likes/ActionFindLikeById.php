<?
namespace GummerD\PHPnew\http\Actions\Likes;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\Likes\LikesNotFoundException;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;

class ActionFindLikeById implements ActionInterface
{
    public function __construct(
        protected LikesRepositoryInterface $likesRepository,
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $like_id = $request->query('like_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $like = $this->likesRepository->getLikesById($like_id);
        } catch (LikesNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
                'like_id'=>"Найден дайк с ID:{$like->getLikeId()}",
                'owner'=> "Лайк создaл пользователь с логином: {$like->getOwnerId()->getUsername()}"
        ]);
    }
}
