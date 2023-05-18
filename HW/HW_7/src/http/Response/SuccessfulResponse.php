<?
namespace GummerD\PHPnew\http\Response;

use GummerD\PHPnew\http\Response\Response;

class SuccessfulResponse extends Response
{
    protected const  SUCCESS = true;

    public function __construct(
        private array $data = []
    ){

    }

    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}