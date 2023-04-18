<?
namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;

class Comment
{
    protected ?UUID $id;
    protected ?User $owner_id;
    protected ?Post $post_id;
    protected ?string $text;

    public function __construct($id = null, $owner_id = null, $post_id = null, $text = null)
    {
       $this->id = $id;
       $this->owner_id = $owner_id;
       $this->post_id = $post_id;
       $this->text = $text;
    }

    public function __toString()
    {
        return "
            Номер комментария: {$this->id} <br>
            Пользователь под номером: {$this->owner_id->getId()} <br>
            Имя и фамилия: {$this->owner_id->getName()->getFirstname()} {$this->owner_id->getName()->getLastname()} <br>
            Номер поста: {$this->post_id->getId()} <br>
            Текст: {$this->text} <br>
        " . PHP_EOL;
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
    public function getOwnerId(): User
    {
        return $this->owner_id;
    }

    /**
     * Set the value of owner_id
     *
     * @return  self
     */ 
    public function setOwnerId($owner_id): self
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    /**
     * Get the value of post_id
     */ 
    public function getPostId(): Post
    {
        return $this->post_id;
    }

    /**
     * Set the value of post_id
     *
     * @return  self
     */ 
    public function setPostId($post_id): self
    {
        $this->post_id = $post_id;

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