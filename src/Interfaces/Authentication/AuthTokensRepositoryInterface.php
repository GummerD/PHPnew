<?
namespace GummerD\PHPnew\Interfaces\Authentication;

use GummerD\PHPnew\Models\AuthToken;




interface AuthTokensRepositoryInterface
{
// Метод сохранения токена
public function save(AuthToken $authToken): void;

// Метод получения токена
public function get(string $token): AuthToken;

// Метод получения токена по id пользователя
public function getId(string $user_id): AuthToken;

// Метод для изменения время действия токена
public function updateTokenExpiresOff(string $token): void;

}