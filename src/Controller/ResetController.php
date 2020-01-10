<?php

namespace Algn\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Algn\Database\Database;


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
class ResetController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    
    
    public function indexAction() {
        $session = $this->di->get("session");
        $res = $this->di->get("response");
        $db = (new Database())->db;
        $sql = file_get_contents(__DIR__ . "/../../sql/ddl.sql");

        $db->exec($sql);
        $session->delete("userid");

        return $res->redirect("profile");
    }

    public function catchAll(...$args) {
        $page = $this->di->get("page");

        $page->add("algn/home/404");

        return $page->render();
    }
}
