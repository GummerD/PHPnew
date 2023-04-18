<?

namespace GummerD\PHPnew\http\Actions\Comments;

use Faker\Factory;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\ErrorResponse;
use GummerD\PHPnew\Exceptions\http\HttpException;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Models\Comment;

class CreateComment implements ActionInterface
{

    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        private CommentsRepositoriesInterface $commentsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        $facker = Factory::create('ru_Ru');

        // Пытаемся создать UUID пользователя и статьи из данных запроса
        try {
            $authorId = new UUID($request->jsonBodyField('user_id'));
            $postId = new UUID($request->jsonBodyField('post_id'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пользователя и статью в репозитории
        try {
            $user = $this->usersRepository->getByUserId($authorId);
            $post = $this->postsRepository->getPostById($postId);
        } catch (UserNotFoundException | PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем UUID для нового комментария
        $newCommentId = UUID::random();

        try {
            // Пытаемся создать объект комментария
            // из данных запроса
            $comment = new Comment(
                $newCommentId,
                $user,
                $post,
                $facker->text(30)
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новуый комментарий в репозитории
        $this->commentsRepository->save($comment);

        // Возвращаем успешный ответ,
        // содержащий UUID нового комменатрия и данные автора
        return new SuccessfulResponse([
            'save_new_comment' => "Новый комментарий, id: {$newCommentId} сохранен.",
            'owner' => "Создатель: {$user->getUsername()}."
        ]);
    }
}
