<?

namespace GummerD\PHPnew\UnitTests\Commands;

use GummerD\PHPnew\Commands\Users\Arguments;
use GummerD\PHPnew\Exceptions\Arguments\ArgumentsExceptions;
use PHPUnit\Framework\TestCase;
/**
 * Summary of ArgumentsTest
 */
class ArgumentsTest extends TestCase
{   

    /**
     * Summary of testItReturnsArgumentsValueByName
     * @return void
     */
    public function testItReturnsArgumentsValueByName()
    {   
        //Подготовака:
        $arguments = new Arguments(['some_key' => 'some_value']); 
        // хорошей практикой будет удалять классы, после тестирования, есть некие методы: teerUp и teerDown
        
        //Действие:
        $value = $arguments->get('some_key'); 

        //Проверка:
        $this->assertEquals('some_value', $value, 'Непарвльно указно значение');// данный метод непроверят типы данных, по этому легко может пройти и integer.
        
        $arguments = new Arguments(['some_key' => 123]); 
        
        $value = $arguments->get('some_key');
        // проверяем значение и тип:
        $this->assertSame('123', $value);

        //Явно проверяем на string:
        $this->assertIsString($value);


    }

    public function testThrowsArgumentsWhenArgumentIsAbsent(): void
    {   // проверка на исключение, здесь сначло описывается исключение из класса (полностью с выдаваемым message)
        // потом вызвается get
        $arguments = new Arguments([]);

        $this->expectException(ArgumentsExceptions::class);

        $this->expectExceptionMessage("Значение не найдено: test_key");

        $arguments->get('test_key');
    }

    public function testItConvertsArgumentsToString(): void
    {
        $arguments = new Arguments(['some_key' => '123']);
        $value = $arguments->get('some_key');
        $this->assertEquals('123', $value);
    }

    /**
     * @dataProvider argumentsProvider
     * @return void
     */
    public function testInConvertsArgumentsToString($inputeValue, $expectedValue): void
    {
        $arguments = new Arguments(['some_key' => $inputeValue]);
        $value = $arguments->get('some_key');
        $this->assertEquals($expectedValue, $value);
    }

    //провайдер данных:
    public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'],
            ['some_string', 'some_string'],
            ['some_string', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
            [12.3, '12.3'],
        ];
    }
}