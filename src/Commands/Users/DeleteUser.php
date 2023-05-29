<?
namespace GummerD\PHPnew\Commands\Users;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class DeleteUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:delete')
            ->setDescription('Удаление пользователя')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Usrename для удаления пользователя'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $question = new ConfirmationQuestion(
            'Удалить пользователя [Y/n]? ',
            false
        );

        
        if (!$this->getHelper('question')
            ->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $username = $input->getArgument('username');

        $this->usersRepository->deleteByUsername($username);

        $output->writeln("Удален пользователь с логином: '{$username}' ");

        return Command::SUCCESS;
    }
}
