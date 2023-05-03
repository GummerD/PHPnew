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
use GummerD\PHPnew\http\Identification\JsonBodyIdentificationUserByUsername;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;

class CreatePost implements ActionInterface
{

    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private JsonBodyIdentificationUserByUsername $identification,
    ) {
    }

    public function handle(Request $request): Response
    {   
        $facker = Factory::create('ru_Ru');

        //ввел идентификацию
        $author = $this->identification->user($request);

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

        return new SuccessfulResponse([
            'save_new_post' => "Новый пост, id: {$newPostId} сохранен.",
            'owner' => "Создатель: {$author->getUsername()}."
        ]);
    }
}
