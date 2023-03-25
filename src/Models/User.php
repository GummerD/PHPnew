<?
namespace GummerD\PHPnew\Models;

class User
{
    protected ?int $id;
    protected ?string $firstname;
    protected ?string $lastname;

    public function __construct($id, $firstname, $lastname)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function __toString()
    {
        return "
            Пользователь под номером: {$this->id},
            имя: {$this->firstname}, 
            фамилия: {$this->lastname}.
        ";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }
}
