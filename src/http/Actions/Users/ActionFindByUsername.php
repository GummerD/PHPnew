<?

namespace GummerD\PHPnew\http\Actions\Users;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\http\Identification\JsonBodyIdentificationUserByUsername;


class ActionFindByUsername implements ActionInterface
{
    public function __construct(
        private JsonBodyIdentificationUserByUsername $identification,
    ) {
    }

    public function handle(Request $request): Response
    {
        // ввел идентификатор
        $user = $this->identification->user($request);

        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'name' => $user->getName()->getFirstname() . ' ' . $user->getName()->getLastname(),
        ]);
    }
}
