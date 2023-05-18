<?
namespace GummerD\PHPnew\Interfaces\Authentication;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}