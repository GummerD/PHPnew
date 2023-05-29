<?
namespace GummerD\PHPnew\Commands\Comments;

use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Models\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommentCreate extends Command
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoriesInterface $postsRepository
    )
    {
        parent:: __construct();
    }

    protected function configure()
    {
        $this->setName('comment:create')->setDescription('Создание комментария')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('post_id', InputArgument::REQUIRED, 'Post id')
            ->addArgument('text', InputArgument::REQUIRED, 'Text for comment');
    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {
        $output->writeln("Попытка создания комментария для статьи с ID: {$input->getArgument('post_id')}");

        $username = $input->getArgument('username');

        $creator = $this->usersRepository->getByUsername($username);

        if ($creator === false) {
            $output->writeln("Пользователя с таим логином: {$username} не существует.");
            return Command::FAILURE;
        }

        $post_id = $input->getArgument('post_id');

        $post = $this->postsRepository->getPostById($post_id);

        if ($post === false) {
            $output->writeln("Статьи с таим id: {$post_id} не существует.");
            return Command::FAILURE;
        }

        $comment_id = UUID::random();

        $comment = new Comment(
            $comment_id,
            $creator,
            $post,
            $input->getArgument('text')
        );

        $this->commentsRepository->save($comment);

        $output->writeln("Пользователем {$creator->getusername()} создан новый комментарий id: {$comment_id}
        к статье: {$post->getTitle()} ");

        return Command::SUCCESS;
    }
}