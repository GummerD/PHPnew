<?

namespace GummerD\PHPnew\Container;

use ReflectionClass;
use Psr\Container\ContainerInterface;
use GummerD\PHPnew\Exceptions\Container\NotFoundException;


/**
 * Summary of DIContainer
 */
class DIContainer implements ContainerInterface
{
    private array $resolvers = [];

    public  function bind(string $type, $variables)
    {
        $this->resolvers[$type] = $variables;
        //var_dump($this->resolvers);
    }

    public function get(string $type): object
    {
        if (array_key_exists($type, $this->resolvers)) {
            
            $typeToCreate =  $this->resolvers[$type];

            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        //var_dump($type);

        if (!class_exists($type)) {
            throw new NotFoundException("Такого класса не было объявлено:{$type}");
        }

        $reflectionClass = new ReflectionClass($type);
        $constructor = $reflectionClass->getConstructor();
        
        if($constructor === null){
            return new $type;
        }

        $parameters = [];

        foreach($constructor->getParameters() as $parameter){
            $parameterType = $parameter->getType()->getName();
            //var_dump($parameterType);
            $parameters[] =  $this->get($parameterType);
            //var_dump($parameters); 
        }

        return new $type(...$parameters);
    }

    public function has(string $type): bool
    {
        try {
            $this->get($type);
            } catch (NotFoundException $e) {
            // Возвращаем false, если объект не создан...
            return false;
            }
            // и true, если создан
            return true;
    }
}
