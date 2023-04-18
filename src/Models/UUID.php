<?
namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Exceptions\UUID\InvalidArgumentException;

class UUID
{
    public function __construct(
        private ?string $uuidString
    ){
        if(!uuid_is_valid($uuidString)){
            throw new InvalidArgumentException(
                "Неправльный формат переменной - UUID: {$this->uuidString}" . PHP_EOL 
            );
        }
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString()
    {
        return $this->uuidString;
    }

    public function uuidString():string
    {
        return $this->uuidString;
    }

}