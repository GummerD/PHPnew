<?
namespace GummerD\PHPnew\Commands\Likes;

use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\Models\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LikeCreate extends Command
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoriesInterface $postsRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('like:create')->setDescription('Create new like')
            ->addArgument('post_id', InputArgument::REQUIRED, 'Id post')
            ->addArgument('username', InputArgument::REQUIRED, 'Username user');
    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {
        $post_id = $input->getArgument('post_id');
        $username = $input->getArgument('username');

        

        $post = $this->postsRepository->getPostById($post_id);

        if ($post === false) {
            $output->writeln("Статьи с таим id: {$post_id} не существует.");
            return Command::FAILURE;
        }

        $user = $this->usersRepository->getByUsername($username);

        if ($user === false) {
            $output->writeln("Пользователя с таким логином: {$username} не существует.");
            return Command::FAILURE;
        }

        if ($this->likesRepository->CheckOwnerInTablelikes($post, $user) === false)
        {
            $output->writeln("Пользователь с логином: {$username} уже поставил лайк  статье {$post->getTitle()}.");
            
            return Command::FAILURE;
        }

        $like_id = UUID::random();

        $like = new Likes(
            $like_id,
            $post,
            $user
        );

        $this->likesRepository->save($like);

        $output->write("Пользователем {$username} поставлен лайк статье: {$post->getTitle()}");

        return Command::SUCCESS;
    }
}