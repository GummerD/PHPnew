<?
namespace GummerD\PHPnew\http\Actions\Likes;

use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\Likes\LikesNotFoundException;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;

class DeleteLike implements ActionInterface
{
    public function __construct(
        protected LikesRepositoryInterface $likesRepository
    ){
    }

    public function handle(Request $request): Response
    {   
        try {
            $like_id = $request->query('like_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $this->likesRepository->getLikesById($like_id);
        } catch (LikesNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->likesRepository->delete($like_id);

        return new SuccessfulResponse([
            'delete_like' => "Like с ID: {$like_id} успешно удален."
        ]);
    }
}