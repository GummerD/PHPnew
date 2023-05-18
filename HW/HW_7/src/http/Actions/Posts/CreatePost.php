<?

namespace GummerD\PHPnew\http\Actions\Posts;

use Faker\Factory;
use Psr\Log\LoggerInterface;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Interfaces\Authentication\TokenAuthenticationInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;

class CreatePost implements ActionInterface
{

    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private TokenAuthenticationInterface $auth,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {   
        $facker = Factory::create('ru_Ru');
        
        $author = $this->auth->user($request);

        $newPostId = UUID::random();

        try {
            $post = new Post(
                $newPostId,
                $author,
                $facker->text(10),
                $facker->text(30)
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        $this->logger->info("Сохранена новая статья с id: {$newPostId}, пользователем с логином: {$author->getUsername()}.");

        return new SuccessfulResponse([
            'save_new_post' => "Новый пост, id: {$newPostId} сохранен.",
            'owner' => "Создатель: {$author->getUsername()}."
        ]);
    }
}
