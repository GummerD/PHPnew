<?

require_once(__DIR__ . '/vendor/autoload.php');

use GummerD\PHPnew\Exceptions\http\Actions\Users\ActionFindByUsername;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\SuccessfulResponse;

try {

    $request = new Request(
        $_GET,
        $_SERVER
    );

    $connection = new \PDO('sqlite:' . __DIR__ . "/blog.sqlite");

    $userRepo = new SqliteUsersRepo($connection);

    //print_r($connection);


    $actionByUsername = new ActionFindByUsername($userRepo);
    $response = $actionByUsername->handle($request);

    $response->send();
    
} catch (\Exception $ex) {
    echo $ex->getMessage();
}
