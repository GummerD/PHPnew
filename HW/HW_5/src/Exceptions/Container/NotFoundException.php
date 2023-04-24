<?
namespace GummerD\PHPnew\Exceptions\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Summary of NotFoundException
 * В данном случае описание исключения 
 * по PSR-11  нужо описывать через реализацию
 * интерфейса NotFoundExceptionInterface
 */
class NotFoundException extends Exception
implements NotFoundExceptionInterface
{
}