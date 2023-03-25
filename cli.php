<?

use Faker\Factory;
use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\Comment;

require_once __DIR__ . "/vendor/autoload.php";

$facker = Factory::create('ru_Ru');

switch ($argv) {
    case $argv[1] === 'user':
        echo new User(
            $facker->randomNumber(),
            $facker->firstName(),
            $facker->lastName()
        );
        break;
    case $argv[1] === 'post':
        echo new Post(
            $facker->randomNumber(),
            $facker->randomNumber(),
            $facker->title(),
            $facker->text()
        );
        break;
    case $argv[1] === 'comment':
        echo new Comment(
            $facker->randomNumber(),
            $facker->randomNumber(),
            $facker->randomNumber(),
            $facker->text()
        );
        break;
}
