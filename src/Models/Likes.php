<?
namespace GummerD\PHPnew\Models;

use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\UUID;

/**
 * Summary of Likes
 * модель реакций пользоватлей на статьи
 */
class Likes
{
    /**
     * Summary of __construct
     * @param UUID $like_id
     * @param Post $post_id
     * @param User $owner_id
     */
    public function __construct(
        protected ?UUID $like_id = null,
        protected ?Post $post_id = null,
        protected ?User $owner_id = null,
    ){

    }

    /**
     * Summary of __toString
     * @return string
     */
    public function __toString()
    {
        return "
            ID лайка: {$this->like_id},
            ID статьи: {$this->getPostId()->getId()},
            ID статьи: {$this->getOwnerId()->getId()},
            Логин пользователя: {$this->getOwnerId()->getUsername()},
        ";
    }

    /**
     * Summary of getLikeId
     * @return UUID
     */
    public function getLikeId(): UUID
    {
        return $this->like_id;
    }

    /**
     * Summary of setLikeId
     * @param mixed $like_id
     * @return Likes
     */
    public function setLikeId($like_id): self
    {
        $this->like_id = $like_id;

        return $this;
    }

    /**
     * Summary of getPostId
     * @return Post
     */
    public function getPostId(): Post
    {
        return $this->post_id;
    }

    /**
     * Summary of setPostId
     * @param mixed $post_id
     * @return Likes
     */
    public function setPostId($post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    /**
     * Summary of getOwnerId
     * @return User
     */
    public function getOwnerId(): User
    {
        return $this->owner_id;
    }

    /**
     * Summary of setOwnerId
     * @param mixed $owner_id
     * @return Likes
     */
    public function setOwnerId($owner_id): self
    {
        $this->owner_id = $owner_id;

        return $this;
    }

}