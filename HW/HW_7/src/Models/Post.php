<?
namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;

class Post
{
    protected ?UUID $id;
    protected ?User $user;
    protected ?string $title;
    protected ?string $text;

    public function __construct($id = null, $user = null, $title = null, $text = null)
    {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString()
    {
        return " 
            Пост номер: {$this->id},
            id пользователя: {$this->user->getId()},
            Имя и фамилия пользователя: {$this->user->getName()->getFirstname()}, {$this->user->getName()->getLastname()},
            Заголовок поста: {$this->title},
            Текст поста: {$this->text}. 
        ";
    }

    /**
     * Get the value of id
     */ 
    public function getId(): UUID
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of owner_id
     */ 
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the value of owner_id
     *
     * @return  self
     */ 
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Get the value of title
     */ 
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of text
     */ 
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @return  self
     */ 
    public function setText($text): self
    {
        $this->text = $text;

        return $this;
    }
}