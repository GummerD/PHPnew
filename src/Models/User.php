<?

namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Models\UUID;
use GummerD\PHPnew\Models\Person\Name;

/**
 * Summary of User
 */
class User
{
    /**
     * Summary of __construct
     * @param UUID|null $id
     * @param string|null $username
     * @param Name|null $name
     */
    public function __construct(
        protected ?UUID $id, 
        protected ?string $username,
        protected ?Name $name
        )
    {}
        
    public function __toString()
    {
        return "
            Пользователь под номером: {$this->id},
            логин: {$this->username},
            имя: {$this->name->getFirstname()}, 
            фамилия: {$this->name->getLastname()}.
        ";
    }

    /**
     * Summary of getId
     * @return UUID
     */
    public function getId(): UUID
    {
        return $this->id;
    }

    /**
     * Summary of setId
     * @param mixed $id
     * @return User
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Summary of getUsername
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Summary of setUsername
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

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
