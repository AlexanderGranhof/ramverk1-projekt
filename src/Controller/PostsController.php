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
class PostsController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    
    
    
    public function indexAction(): object {
        $page = $this->di->get("page");
        $post = new Post();

        $allPosts = $post->all();

        $page->add("algn/posts/index", [
            "posts" => $allPosts
        ]);

        return $page->render();
    }

    public function newAction(): object {

        // $session = $this->di->get("session");

        $page = $this->di->get("page");

        $page->add("algn/posts/create");

        return $page->render();
    }

    public function newActionPost(): string {
        $req = $this->di->get("request");
        $res = $this->di->get("response");
        $session = $this->di->get("session");

        $userid = $session->get("userid");

        $body = $req->getPost();

        $title = $body["title"] ?? null;
        $content = $body["content"] ?? null;

        if (is_null($title) || is_null($content) || !$userid) {
            $session->set("error_posts", true);
            return $res->redirect("posts");
        }

        $post = new Post();

        $result = $post->create($userid, $title, $content);
        $id = $result["id"] ?? null;

        if (!$id) {
            $session->set("error_posts", true);
            return $res->redirect("posts");
        }

        return $res->redirect("posts/$id");
    }

    public function commentActionPost(): string {
        $req = $this->di->get("request");
        $session = $this->di->get("session");
        $post = new Post();
        
        $userid = $session->get("userid");

        $body = $req->getPost();

        $id = $body["id"];
        $content = $body["content"];

        $result = $post->comment($id, $userid, $content);

        return $result ? "true" : "false";
    }

    public function commentVoteActionPost(): string {
        $req = $this->di->get("request");
        $session = $this->di->get("session");

        $body = $req->getPost();

        $score = $body["score"] ?? null;
        $id = $body["id"] ?? null;


        if (is_null($score) || is_null($id)) {
            return "false";
        }

        $score = intval($score);
        $id = intval($id);


        if (is_nan($score) || is_nan($id)) {
            return "false";
        }

        if (!($score <= 1 && $score >= -1)) {
            return "false";
        }

        $post = new Post();
        
        $uid = $session->get("userid");
        $result = $post->commentVote($id, $uid, $score);

        return $result ? "true" : "false";
    }

    public function postVoteActionPost(): string {
        $req = $this->di->get("request");
        $session = $this->di->get("session");

        $body = $req->getPost();

        $score = $body["score"] ?? null;
        $id = $body["id"] ?? null;


        if (is_null($score) || is_null($id)) {
            return "false";
        }

        $score = intval($score);
        $id = intval($id);


        if (is_nan($score) || is_nan($id)) {
            return "false";
        }

        if (!($score <= 1 && $score >= -1)) {
            return "false";
        }

        $post = new Post();
        
        $uid = $session->get("userid");
        var_dump($id, $uid, $score);
        $result = $post->postVote($id, $uid, $score);


        return $result ? "true" : "false";
    }

    public function catchAll($route) {
        $post = new Post();
        $res = $this->di->get("response");
        $req = $this->di->get("request");
        $page = $this->di->get("page");
        $session = $this->di->get("session");

        $uid = $session->get("userid");

        if (!preg_match("/[0-9]/", $route)) {
            return $res->redirect("posts");
        }

        $id = intval($route);

        $singlePost = $post->get($id);

        if (!$singlePost) {
            return $res->redirect("posts");
        }

        $userUpvoted = $post->userUpvotedComments($uid, $id);
        $sort = $req->getGet("sort");

        // var_dump($singlePost);

        $comments = $post->getComments($singlePost["id"], $sort);

        $postScore = $post->postScore($uid, $id);

        $postScore = isset($postScore["score"]) ? $postScore["score"] : null;
        

        $page->add("algn/posts/single", [
            "post" => $singlePost,
            "comments" => $comments,
            "userUpvoted" => $userUpvoted,
            "postScore" => $postScore
        ]);

        return $page->render();
    }
}
