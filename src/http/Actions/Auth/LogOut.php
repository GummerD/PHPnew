<?
namespace GummerD\PHPnew\http\Actions\Auth;

use DateTimeImmutable;
use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;
use GummerD\PHPnew\http\Response\SuccessfulResponse;
use GummerD\PHPnew\http\Actions\Interfaces\ActionInterface;
use GummerD\PHPnew\Interfaces\Authentication\AuthenticationInterface;
use GummerD\PHPnew\Interfaces\Authentication\AuthTokensRepositoryInterface;
use GummerD\PHPnew\Interfaces\Authentication\PasswordAuthenticationInterface;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokensRepositoryInterface $authRepo,
        private PasswordAuthenticationInterface $passwordAuthentication
    )
    {
    }

    public function handle(Request $request): Response
    {
        
        $user = $this->passwordAuthentication->user($request);
        
        $UserTokenRepo = $this->authRepo->getId($user->getId());
        
        $token = $UserTokenRepo->token();

        $this->authRepo->updateTokenExpiresOff($token);

        return new SuccessfulResponse(
            [
                "logout"=>"Пользователь {$user->getUsername()} вышел из приложения"
            ]
        );
    }
}