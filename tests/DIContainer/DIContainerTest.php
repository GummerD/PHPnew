<?

namespace GummerD\PHPnew\UnitTests\DIContainer;

use PHPUnit\Framework\TestCase;
use GummerD\PHPnew\Container\DIContainer;
use GummerD\PHPnew\Exceptions\Container\NotFoundException;
use GummerD\PHPnew\Interfaces\IRepositories\UsersRepositoryInterface;
use GummerD\PHPnew\Repositories\UserRepo\MemoryRepoUsers;
use GummerD\PHPnew\Repositories\UserRepo\SqliteUsersRepo;
use GummerD\PHPnew\UnitTests\DIContainer\SomeClass\SomeClassTest;
use GummerD\PHPnew\UnitTests\DIContainer\SomeClass\SomeInterfaceTest;
use GummerD\PHPnew\UnitTests\DIContainer\SomeClass\SomeClassWithoutDependecies;
use GummerD\PHPnew\UnitTests\DIContainer\SomeClass\SomeClassWithParameterTest;

//use GummerD\PHPnew\Exceptions\Container\SomeClassWithoutDependecies;


class DIContainerTest extends TestCase
{

    public function testItThrowNotFoundException(): void
    {   
        $type = '';

        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Неверный тип переменной:{$type}"
        );

        $container->get(SomeClass::class);
    }

    public function testItHceksForTheExistenceClass(): void
    {
        $container = new DIContainer();

        $object = $container->get(SomeClassWithoutDependecies::class);

        $this->assertInstanceOf(SomeClassWithoutDependecies::class, $object);
        
    }

    public function testItHceksResolversClasses(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeInterfaceTest::class, 
            SomeClassTest::class
        );
        
        $object = $container->get(SomeInterfaceTest::class);

        $this->assertInstanceOf(SomeClassTest::class, $object);

    }

    public function testItHceksResolversClassesUserRepo(): void
    {
        $container = new DIContainer();
        $container->bind(
            UsersRepositoryInterface::class, 
            MemoryRepoUsers::class
        );

        $object = $container->get(UsersRepositoryInterface::class);

        $this->assertInstanceOf(MemoryRepoUsers::class, $object);
    }

    public function testItHceksClassWithParameter(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameterTest::class, 
            new SomeClassWithParameterTest(42)
        );

        $object = $container->get(SomeClassWithParameterTest::class,);

        $this->assertInstanceOf(SomeClassWithParameterTest::class, $object);

        $this->assertSame(42,$object->value());
    }
}
