<?
namespace GummerD\PHPnew\http\Actions\Interfaces;

use GummerD\PHPnew\http\Request;
use GummerD\PHPnew\http\Response\Response;

interface ActionInterface 
{
    public function handle(Request $request): Response;
}