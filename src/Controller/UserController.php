<?php

namespace Algn\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Algn\Database\User;


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
class UserController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    
    public function indexAction(): object {
        $page = $this->di->get("page");
        $session = $this->di->get("session");
        $res = $this->di->get("response");

        $uid = $session->get("userid");
        
        if ($uid) {
            $user = new User();
            $userData = $user->get($uid);

            if ($userData) {
                $username = $userData["username"];

                return $res->redirect("profile/$username");
            }
        }

        $page->add("algn/profile/login");

        return $page->render();
    }

    public function indexActionPost(): object {
        $req = $this->di->get("request");
        $res = $this->di->get("response");
        $page = $this->di->get("page");
        $session = $this->di->get("session");
        $user = new User();        

        $body = $req->getPost();

        $username = $body["username"] ?? null;
        $password = $body["password"] ?? null;
        $signout = $body["signout"] ?? null;

        if ($signout) {
            $session->delete("userid");
            return $res->redirect("profile");
        }

        $data = $user->verify($username, $password);

        if (!$data["verified"]) {
            $session->set("error_login", true);
        } else {
            $session->set("userid", $data["id"]);
        }

        return $res->redirect("profile");
    }

    public function modActionPost() {
        $req = $this->di->get("request");
        $res = $this->di->get("response");
        $user = new User();

        $body = $req->getPost();

        $userID = $body["user-id"] ?? null;
        $userName = $body["user-name"] ?? null;

        if (!is_null($userID)) {
            $id = intval($userID);

            if (!is_nan($id)) {
                $user->grantMod($id);
            }
        }

        if ($userName) {
            return $res->redirect("profile/$userName");
        }
        
        return $res->redirect("profile");
    }

    public function registerAction(): object {
        $page = $this->di->get("page");

        $page->add("algn/profile/register");

        return $page->render();
    }

    public function  registerActionPost(): object {
        $req = $this->di->get("request");
        $res = $this->di->get("response");
        $session = $this->di->get("session");

        $user = new User();

        $body = $req->getPost();

        $username = $body["username"] ?? null;
        $password = $body["password"] ?? null;
        $email = $body["email"] ?? null;

        if (!$username || !$password || !$email) {
            $session->set("error_register", true);
            return $res->redirect("profile/register");
        }

        $result = $user->register($username, $password, $email);

        if ($result["id"]) {
            $session->set("userid", $result["id"]);
        }

        if (isset($result["err"])) {
            $session->set("register_error_username", true);
        }
        
        return $res->redirect("profile");
    }

    public function catchAll(...$args): object {
        [$route] = $args;

        $page = $this->di->get("page");
        $session = $this->di->get("session");
        $userdb = new User();

        $userid = $session->get("userid");

        $user = $userdb->getFromName($route);
        $comments = $userdb->comments($route);
        $activity = $userdb->activity($user["id"]);
        $score = $userdb->score($route);

        $loggedInUser = $userdb->get($userid);

        $page->add("algn/profile/profile", [
            "user" => $user,
            "comments" => $comments,
            "activity" => $activity,
            "score" => $score,
            "loggedInUser" => $loggedInUser
        ]);

        return $page->render();
    }
}
