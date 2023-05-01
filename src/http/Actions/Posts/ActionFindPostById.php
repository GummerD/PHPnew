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
use Psr\Log\LoggerInterface;

class ActionFindPostById implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private LoggerInterface $logger
    ) {
    }

    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $post_id = $request->query('post_id');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $post = $this->postsRepository->getPostById($post_id);
        } catch (PostNotFoundException $e) {
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info(
            "Инициализирован поиск статьи с id {$post_id}"
        );
    
        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'post_id' =>  $post->getId()->uuidString(), 
            'owner_name' => $post->getUser()->getName()->getFirstname() . ' ' . $post->getUser()->getName()->getLastname(),
        ]);
    }
}
