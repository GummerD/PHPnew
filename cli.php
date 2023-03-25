<?

use Faker\Factory;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\Comment;

require_once __DIR__ . "/vendor/autoload.php";

$facker = Factory::create('ru_Ru');
$user =  new User(
    $facker->randomNumber(),
    $facker->firstName(),
    $facker->lastName()
);

$post = new Post(
    $facker->randomNumber(),
    $facker->randomNumber(),
    $facker->title(),
    $facker->text()
);

$comment = new Comment(
    $facker->randomNumber(),
    $facker->randomNumber(),
    $facker->randomNumber(),
    $facker->text()
);


switch ($argv) {
    case $argv[1] === 'user':
        echo $user;
        break;
    case $argv[1] === 'post':
        echo $post;
        break;
    case $argv[1] === 'comment':
        echo $comment;
        break;
}
