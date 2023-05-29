<?
namespace GummerD\PHPnew\Commands\Users;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GummerD\PHPnew\Exceptions\UsersExceptions\UserNotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('user:update')->setDescription('Update for user table')
            ->addArgument('username', InputArgument::REQUIRED, 'Username for user update table')
            ->addOption('first-name', 'f', InputOption::VALUE_OPTIONAL, 'First name')
            ->addOption('last-name', 'l', InputOption::VALUE_OPTIONAL, 'Last name');
    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {
        
        
        $first_name = $input->getOption('first-name');
        //var_dump($first_name);
        $last_name = $input->getOption('last-name');

        if(empty($first_name) && empty($last_name)){

            $output->writeln('Не указаны данные пользователя: first_name и last_name');

            return Command::FAILURE;
            
        }

        $username = $input->getArgument('username');
        //var_dump($username);
        
        $output->writeln("Попытка обновить данные о пользователе c логином: {$username}");

        try{
            $user = $this->usersRepository->getByUsername($username);
        }catch(UserNotFoundException $e)
        {
            $e->getMessage();
        }

        $updateName = new Name(
            empty($first_name) ? $user->getName()->getFirstname() : $first_name,
            empty($last_name) ? $user->getName()->getLastname() : $last_name
        );

        //var_dump($updateName);

        $updateUser = new User (
            $user->getId(),
            $username,
            $user->getPassword(),
            $updateName
        );

        $this->usersRepository->saveForCommandConsole($updateUser);

        $output->writeln("Изменены данные у пользователя под логиномы: {$username}");

        return Command::SUCCESS;
    }
}