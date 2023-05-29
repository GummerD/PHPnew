<?

namespace GummerD\PHPnew\Commands\Likes;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GummerD\PHPnew\Exceptions\Likes\LikesNotFoundException;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;

class LikeDelete extends Command
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('like:delete')->setDescription('Command for deleting a like')
            ->addArgument('like_id', InputArgument::REQUIRED, 'For a like id');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $question = new ConfirmationQuestion(
            'Удалить лайк [Y/n]? ',
            false
        );

        if (!$this->getHelper('question')
            ->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }


        $like_id = $input->getArgument('like_id');

        try {

            $this->likesRepository->getLikesById($like_id);
        } catch (LikesNotFoundException $e) {
            $e->getMessage();

            $output->writeln("В таблице БД лайка с таким id: {$like_id} нет");

            return Command::FAILURE;
        }

        $this->likesRepository->delete($like_id);

        $output->writeln("Из БД удален лайк с id: {$like_id}");

        return Command::SUCCESS;
    }
}
