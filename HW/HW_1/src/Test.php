<?  
namespace GummerD\PHPnew;

class Test
{
    public string $name;

    /*
    public function __construct($name)
    {
        $this->name = $name;
    }
    */

    public function getName($name)
    {   
        
        $this->name = $name;
        echo "Привет {$this->name}" ;
        
        
    }
}