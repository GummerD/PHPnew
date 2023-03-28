<?
namespace GummerD\PHPnew\Models;

class Comment
{
    protected ?int $id;
    protected ?int $owner_id;
    protected ?int $post_id;
    protected ?string $text;

    public function __construct($id, $owner_id, $post_id, $text)
    {
       $this->id = $id;
       $this->owner_id = $owner_id;
       $this->post_id = $post_id;
       $this->text = $text;
    }

    public function __toString()
    {
        return "
            Номер статьи: {$this->id}
            Пользователь под номером: {$this->owner_id}
            Номер поста: {$this->post_id}
            Текст: {$this->text}
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
     * Get the value of post_id
     */ 
    public function getPost_id()
    {
        return $this->post_id;
    }

    /**
     * Set the value of post_id
     *
     * @return  self
     */ 
    public function setPost_id($post_id)
    {
        $this->post_id = $post_id;

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