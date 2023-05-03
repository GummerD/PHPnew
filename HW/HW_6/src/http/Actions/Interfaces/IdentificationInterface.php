<?
namespace GummerD\PHPnew\http\Actions\Interfaces;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}