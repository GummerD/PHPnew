<?
namespace GummerD\PHPnew\Commands\Comments;

use GummerD\PHPnew\Exceptions\CommentsExceptions\CommentNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;

class CommentDelete extends Command
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('comment:delete')->setDescription('Удаляет комментарий по id')
            ->addArgument('comment_id', InputArgument::REQUIRED, 'Id comment');
    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {   
        $question = new ConfirmationQuestion(
            'Удалить комментария [Y/n]? ',
            false
        );

        if (!$this->getHelper('question')
            ->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $comment_id = $input->getArgument('comment_id');

        $output->write("Попытка удаления комментария с id: {$comment_id}");

        try {
            
            $this->commentsRepository->getCommentById($comment_id);

        } catch (CommentNotFoundException $e) {

            $e->getMessage();

            $output->writeln("Комментария с таим id{$comment_id} не существует.");

            return Command::FAILURE;
        }

        $this->commentsRepository->delete($comment_id);

        $output->writeln("Комментарий с id {$comment_id} был удален");

        return Command::SUCCESS;
        
    }
}