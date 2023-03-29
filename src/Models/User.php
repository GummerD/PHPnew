<?

namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;

class User
{
    public function __construct(
        protected ?UUID $id, 
        protected ?Name $name
        )
    {}
        
    public function __toString()
    {
        return "
            Пользователь под номером: {$this->id},
            имя: {$this->name->getFirstname()}, 
            фамилия: {$this->name->getLastname()}.
        ";
    }

    public function getId(): UUID
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }
    
	/**
	 * @return Name|null
	 */
	public function getName(): ?Name 
    {
		return $this->name;
	}
}
