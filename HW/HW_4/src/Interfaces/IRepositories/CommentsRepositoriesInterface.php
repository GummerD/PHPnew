<?
namespace GummerD\PHPnew\Interfaces\IRepositories;

use GummerD\PHPnew\Models\Comment;


/**
 * Summary of CommentsRepositoriesInterface
 */
interface CommentsRepositoriesInterface
{
    /**
     * Summary of save
     * @param Comment $post
     * @return void
     */
    public function save(Comment $post): void;
    /**
     * Summary of getAll
     * @return Comment
     */
    public function getAllComments(): void;
    /**
     * Summary of getPostById
     * @param string $id
     * @return Comment
     */
    public function getCommentById($id): Comment;
    /**
     * Summary of getPostById
     * @param string $owner_id
     * @return Comment
     */
    public function getCommentByOwner_id($owner_id):Comment;
    public function delete($id): void;
}