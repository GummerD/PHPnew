<?

namespace GummerD\PHPnew\http\Response;

use GummerD\PHPnew\http\Response\Response;

/**
 * Summary of ErrorResponse
 * Класс неуспешного ответа
 */
class ErrorResponse extends Response
{

    protected const SUCCESS = false;

    /**
     * Summary of __construct
     * @param string $reason
     * Неуспешный ответ содержит строку с причиной неуспеха,
     * по умолчанию - 'Something goes wrong'
     */
    public function __construct(
        private string $reason = 'Something goes wrong'
    ) {
    }


    /**
     * Summary of payload
     * Реализация абстрактного метода
     * родительского класса
     * @return array
     */
    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}
