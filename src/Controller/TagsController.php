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
class TagsController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    
    
    public function indexAction(): object {
        $page = $this->di->get("page");
        $req = $this->di->get("request");
        $post = new Post();

        $body = $req->getGet();

        $searchTags = $body["tags"] ?? "";

        if(strlen($searchTags) > 0) {    
            $searchTags = explode(",", $searchTags);

            $searchTags = array_filter($searchTags, function($row) {
                return strlen($row);
            });
    
            foreach($searchTags as &$searchTagd) {
                $searchTagd = trim(strtolower($searchTagd));
            }

            
            $tags = $post->popularTags();
            
            $finalTags = [];
            
            foreach($tags as $tag => $score) {
                foreach($searchTags as $searchTag) {
                    if (strpos($tag, $searchTag) !== False) {
                        $finalTags[] = $tag;
                    }
                }
            }

            $page->add("algn/tags/index", [
                "tags" => $finalTags
            ]);

            return $page->render();
        }

        $page->add("algn/tags/index");

        return $page->render();
    }

    public function catchAll(...$args) {
        $page = $this->di->get("page");

        $page->add("algn/home/404");

        return $page->render();
    }
}
