<?php

namespace Algn\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Algn\Database\User;
use Algn\Database\Post;


// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 * The controller will be injected with $di if implementing the interface
 * ContainerInjectableInterface, like this sample class does.
 * The controller is mounted on a particular route and can then handle all
 * requests for that mount point.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class HomeController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    
    
    
    public function indexAction(): object {
        $page = $this->di->get("page");

        $post = new Post();
        $user = new User();


        $tags = $post->popularTags();
        $comments = $post->topComments();
        $posts = $post->top();

        $users = $user->all();;

        foreach($users as &$row) {
            $row["score"] = $user->score($row["id"]);
        }

        usort($users, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        $page->add("algn/home/index", [
            "tags" => $tags,
            "comments" => $comments,
            "users" => $users,
            "posts" => $posts
        ]);

        return $page->render();
    }
}
