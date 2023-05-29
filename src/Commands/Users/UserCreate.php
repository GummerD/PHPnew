<?

namespace GummerD\PHPnew\Commands\Users;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Models\UUID;

class UserCreate extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:create')->setDescription('Creates new user')
            ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $output->writeln('Попытка создания новго пользователя через консоль');

        $username = $input->getArgument('username');

        if ($this->usersRepository->UserExists($username)) {
            $output->writeln("Пользователь с таим логином: {$username} уже существует.");
            return Command::FAILURE;
        }

        $id =  UUID::random();
        $password = $input->getArgument('password');
        $password =  hash('sha256', $id . $password);

        $user = new User(
            $id,
            $username,
            $password,
            new Name(
                $input->getArgument('first_name'),
                $input->getArgument('last_name')
            )
        );

        $this->usersRepository->save($user);

        $output->writeln('Создан новый пользователь: ' . $user->getUsername());

        return Command::SUCCESS;
    }
}
