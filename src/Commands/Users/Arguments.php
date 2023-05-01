<?

namespace GummerD\PHPnew\Commands\Users;

use GummerD\PHPnew\Exceptions\Arguments\ArgumentsExceptions;
use GummerD\PHPnew\Exceptions\CommadsExceptions\CommandException;

class Arguments
{   
    private array $arguments =[];

    public function __construct(iterable $arguments)
    {

        foreach($arguments as $argument => $value){
            $stringValue = trim((string)$value);
            if(empty($stringValue)){
                continue;
            }
    
            $this->arguments[(string)$argument] = $stringValue;
        }

    }

    public static function fromArgv(array $argv): self
    {
        //print_r($argv);
        $input = [];

        foreach ($argv as $argument) {
            //print_r($argument);
            $parts = explode('=', $argument);
            //print_r($parts);
            if (count($parts) !== 2) {
                //var_dump($parts);
                continue;
            }
            $input[$parts[0]] = $parts[1];
        }
        //print_r($input);

        return new self($input);
    }

    public function get(string $argument){
        if (!array_key_exists($argument, $this->arguments)){
                throw new ArgumentsExceptions("Значение не найдено: {$argument}"
            );
        }

        return $this->arguments[$argument];
    }
}
