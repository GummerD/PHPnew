<?

namespace GummerD\PHPnew\Commands\PopulateDB;

use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Likes;
use GummerD\PHPnew\Models\Comment;
use GummerD\PHPnew\Models\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use GummerD\PHPnew\Interfaces\IRepositories\LikesRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Interfaces\IRepositories\PostsRepositoriesInterface;
use GummerD\PHPnew\Interfaces\IRepositories\CommentsRepositoriesInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $generator,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoriesInterface $postsRepository,
        private CommentsRepositoriesInterface  $commentsRepository,
        private LikesRepositoryInterface $likesRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('populateDB:create')->setDescription('For populate DB')
            ->addOption('number-of-users', 'u', InputOption::VALUE_OPTIONAL, 'Set number of users')
            ->addOption('number-of-posts', 'p', InputOption::VALUE_OPTIONAL, 'Set number op posts')
            ->addOption('number-of-comments', 'c', InputOption::VALUE_OPTIONAL, 'Set number of comments')
            ->addOption('number-of-likes', 'l', InputOption::VALUE_OPTIONAL, 'Set number og likes');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $numberOfUsres = $input->getOption('number-of-users');
        $numberOfPosts = $input->getOption('number-of-posts');
        $numberOfComments = $input->getOption('number-of-comments');
        $numberOfLikes = $input->getOption('number-of-likes');

        if(empty($numberOfUsres) && empty($numberOfPosts) && empty($numberOfComments) && empty($numberOfLikes)){
            $output->writeln("Не указан один из параметоров запроса (number-of-users/u, number-of-posts/p, number-of-comments/c, number-of-likes/l) для создания тестовых данных");
            return Command::FAILURE;
        }

        $output->writeln("Инициализирован процесс создания тестовых данных для таблиц: users, posts, comments и likes в  DB - blog.sqlite");

        $question = new ConfirmationQuestion(
            'Populate DB [Y/n]? ',
            false
            );
            
            if (!$this->getHelper('question')
            ->ask($input, $output, $question)
            ) {
            return Command::SUCCESS;
            }

        $users = [];

        for ($i = 0; $i < $numberOfUsres; $i++) {
            $user = $this->createFakeUsers();
            $output->writeln("Создан новый пользователь: {$user->getUsername()}");
            $users[] = $user;
        }

        $posts = [];

        foreach ($users as $user) {
            for ($i = 0; $i <= $numberOfPosts; $i++) {
                $newPost = $this->createFakePosts($user);
                $output->writeln("Создана новая сатья: {$newPost->getTitle()}");
                $this->postsRepository->save($newPost);
                $posts[] = $newPost;
            }
        }

        $comments = [];

        foreach ($users as $user) {
            foreach ($posts as  $post) {
                for ($i = 0; $i <= $numberOfComments; $i++) {
                    $newComment = $this->createFakeComments($user, $post);
                    $output->writeln("Создан новый комментарий c текстом: {$newComment->getText()}");
                    $this->commentsRepository->save($newComment);
                    $comments[] = $newComment;
                }
            }
        }

        $likes = [];

        foreach($users as $user){
            foreach ($posts as  $post) {
                for ($i = 0; $i <= $numberOfLikes; $i++) {
                    $newLike = $this->createFakeLikes($post, $user);
                    $output->writeln("Поставлен новый лайк id: {$newLike->getLikeId()}");
                    $this->likesRepository->save($newLike);
                    $likes[] = $newLike;
                }
            }
        }


        $output->writeln("Тестовые данные для таблиц: users, posts, comments и likes загружены в DB");

        return Command::SUCCESS;
    }

    private function createFakeUsers(): User
    {
        UUID::random();
        $this->generator->password();

        $user = new User(
            UUID::random(),
            $this->generator->userName(),
            $this->generator->password(),
            new Name(
                $this->generator->firstName(),
                $this->generator->lastName()
            )
        );

        $this->usersRepository->save($user);

        return $user;
    }

    private function createFakePosts(User $user): Post
    {
        return new Post(
            UUID::random(),
            $user,
            $this->generator->title(),
            $this->generator->text(50),

        );
    }

    private function createFakeComments(User $user, Post $post): Comment
    {
        return new Comment(
            UUID::random(),
            $user,
            $post,
            $this->generator->text(10),
        );
    }

    private function createFakeLikes(Post $post, User $user): Likes
    {
        return new Likes(
            UUID::random(),
            $post,
            $user
        );
    }
}
