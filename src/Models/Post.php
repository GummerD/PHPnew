<?
namespace GummerD\PHPnew\Models;

class Post
{
    protected ?int $id;
    protected ?int $owner_id;
    protected ?string $title;
    protected ?string $text;

    public function __construct($id, $owner_id, $title, $text)
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
            Текст поста: {$this->text}. <br>
        ";
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of owner_id
     */ 
    public function getOwner_id()
    {
        return $this->owner_id;
    }

    /**
     * Set the value of owner_id
     *
     * @return  self
     */ 
    public function setOwner_id($owner_id)
    {
        $this->owner_id = $owner_id;

        return $this;
    }


    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of text
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @return  self
     */ 
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
}