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
class PopularController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    
    
    
    public function indexAction(): object
    {
        $page = $this->di->get("page");

        $post = new Post();
        $user = new User();


        $tags = $post->popularTags();
        $comments = $post->topComments();
        $posts = $post->top();

        $users = $user->all();

        foreach ($users as &$row) {
            $row["score"] = $user->score($row["id"]);
        }

        usort($users, function ($abc, $bcd) {
            return $bcd['score'] - $abc['score'];
        });

        $topUser = array_shift($users);

        $score = $user->score($topUser["id"]);

        $page->add("algn/home/popular", [
            "tags" => $tags,
            "comment" => array_shift($comments),
            "user" => $topUser,
            "score" => $score,
            "post" => array_shift($posts)
        ]);

        return $page->render(["title" => "Popular", "baseTitle" => " | FoodFlow"]);
    }

    public function catchAll(...$args)
    {
        $args = $args;
        $page = $this->di->get("page");

        $page->add("algn/home/404");

        return $page->render(["title" => "404", "baseTitle" => " | FoodFlow"]);
    }
}
