<?

namespace GummerD\PHPnew\http\Actions\Posts;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use InvalidArgumentException;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $post_id = $request->query('post_id');
            $this->postsRepository->getPostById($post_id);
        } catch (HttpException | PostNotFoundException $e) {  
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->delete($post_id);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'post_delete' =>  "Пост с id: {$post_id} удален.", 
        ]);
    }
}
