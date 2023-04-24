<?
namespace GummerD\PHPnew\http\Actions\Likes;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\Likes\LikesNotFoundException;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;

class ActionFindAllPostsByLikeId implements ActionInterface
{
    public function __construct(
        protected PostsRepositoriesInterface $postsRepositoriy,
        protected LikesRepositoryInterface $likesRepository
    ){

    }

    public function handle(Request $request):Response
    {
        try {
            $post_id = $request->query('post_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepositoriy->getPostById($post_id);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $countLikes=$this->likesRepository->getAllLikesForPost($post_id);
        } catch (LikesNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'number_likes' => "Статье ID: {$post->getId()} поставлено {$countLikes} лайков."
        ]);
    }
}