<?

namespace GummerD\PHPnew\Commands\Posts;

use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Models\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Models\Post;

class PostsCreate extends Command
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:create')->setDescription('Создание новой статьи')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('title', InputArgument::REQUIRED, 'Title')
            ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $output->writeln('Попытка создания новой статьи');

        $username = $input->getArgument('username');

        try {
            $creator =  $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            $e->getMessage();
            return Command::FAILURE;
        }


        $post_id =  UUID::random();

        $newPost = new Post(
            $post_id,
            $creator,
            $input->getArgument('title'),
            $input->getArgument('text')

        );

        $this->postsRepository->save($newPost);

        $output->writeln("Пользователем под ником: {$creator->getUsername()} cоздана новая статья с id: {$newPost->getId()}");

        return Command::SUCCESS;
    }
}
