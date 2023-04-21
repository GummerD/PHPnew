<?
namespace GummerD\PHPnew\Interfaces\IRepositories;

use GummerD\PHPnew\Models\Post;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Models\Likes;

/**
 * Summary of LikesRepositoryInterface
 */
interface LikesRepositoryInterface
{
    /**
     * Summary of save
     * @return void
     */
    public function save(Likes $likes): void;

    public function getLikesById($id): Likes;
    public function getLikesByOwnerId($id): Likes;
    public function getLikesByPostId($id): Likes;
    public function CheckOwnerInTablelikes(Post $post_id, User $owner_id): bool;
    public function delete($like_id): void;
}