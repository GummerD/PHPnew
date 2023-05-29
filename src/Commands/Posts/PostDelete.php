<?
namespace GummerD\PHPnew\Commands\Posts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use GummerD\PHPnew\Exceptions\PostsExceptions\PostNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;

class PostDelete extends Command
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
    )
    {
        parent:: __construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('post:delete')->setDescription('Удаление статьи')
            ->addArgument('post_id', InputArgument::REQUIRED, 'Post_id');

    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {   
        $question = new ConfirmationQuestion(
            'Удалить статью [Y/n]? ',
            false
        );

        if (!$this->getHelper('question')
            ->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $post_id = $input->getArgument('post_id');

        $output->writeln("Попытка удаления статья с id: {$post_id}");

        try {
            $this->postsRepository->getPostById($post_id);
        } catch (PostNotFoundException $e) {

            $e->getMessage();

            return Command::FAILURE; 
        }

        $this->postsRepository->delete($post_id);

        $output->writeln("Cтатья с id: {$post_id} удалена");

        return Command::SUCCESS;
    }
}