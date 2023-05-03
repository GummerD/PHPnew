<?
namespace GummerD\PHPnew\Interfaces\IRepositories;

use GummerD\PHPnew\Models\Post;

/**
 * Summary of PostsRepositoriesInterface
 */
interface PostsRepositoriesInterface{
    
    /**
     * Summary of save
     * @param Post $post
     * @return void
     */
    public function save(Post $post): void;
    /**
     * Summary of getAll
     * @return Post
     */
    public function getAllPosts(): void;
    /**
     * Summary of getPostById
     * @param string $id
     * @return Post
     */
    public function getPostById($id): Post;
    /**
     * Summary of getPostById
     * @param string $title
     * @return Post
     */
    public function getPostByTitle($title):Post;

    public function delete($id): void;

}