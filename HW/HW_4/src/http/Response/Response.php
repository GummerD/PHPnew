<?
declare(strict_types=1);

namespace GummerD\PHPnew\http\Response;


/**
 * Summary of Response
 * Абстрактный класс ответа,
 * содержащий общую функциональность
 * успешного и неуспешного ответа

 */
abstract class Response
{

    // Маркировка успешности ответа
    protected const SUCCESS = true;

    /**
     * Summary of send
     * @return void
     * Метод для отправки ответа
     */
    public function send(): void

    {

        // Данные ответа:
        // маркировка успешности и полезные данные
        $data = ['success' => static::SUCCESS] + $this->payload();

        // Отправляем заголовок, говорщий, что в теле ответа будет JSON
        header('Content-Type: application/json');

        // Кодируем данные в JSON и отправляем их в теле ответа
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    // Декларация абстрактного метода,
    // возвращающего полезные данные ответа
    /**
     * Summary of payload
     * @return array
     */
    abstract protected function payload(): array;
}
