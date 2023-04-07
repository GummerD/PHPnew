<?
namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Models\UUID;

class Post
{
    protected ?UUID $id;
    protected ?UUID $owner_id;
    protected ?string $title;
    protected ?string $text;

    public function __construct($id = null, $owner_id = null, $title = null, $text = null)
    {
        $this->id = $id;
        $this->owner_id = $owner_id;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString()
    {
        return " 
            Статья номер: {$this->id},
            id пользователя: {$this->owner_id},
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
    public function getOwnerId(): UUID
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