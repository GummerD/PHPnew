<?

namespace GummerD\PHPnew\UnitTests\DIContainer\SomeClass;

class SomeClassWithParameterTest
{
    public function __construct(
        private int $value
    ) {
    }

    public function value(): int
    {
        return $this->value;
    }
}
