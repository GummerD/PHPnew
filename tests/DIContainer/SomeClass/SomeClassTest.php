<?
namespace GummerD\PHPnew\UnitTests\DIContainer\SomeClass;

use GummerD\PHPnew\UnitTests\DIContainer\SomeClass\SomeInterface;

class SomeClassTest implements SomeInterfaceTest
{
    public function someFunction($some_value):mixed
    {
        return $some_value;
    }
}