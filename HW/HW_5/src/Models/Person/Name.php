<?
namespace GummerD\PHPnew\Models\Person;

class Name
{   
    //protected ?int $id;
    protected ?string $firstname;
    protected ?string $lastname;
    
    public function __construct($firstname, $lastname)
    {
        //$this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
	 * @return int|null
	 */
	/*
    public function getId(): ?int 
    {
		return $this->id;
	}
	 */
	/**
	 * @param int|null $id 
	 * @return self
	 */
    /*
	public function setId(?int $id): self 
    {
		$this->id = $id;
		return $this;
	}
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

	
}