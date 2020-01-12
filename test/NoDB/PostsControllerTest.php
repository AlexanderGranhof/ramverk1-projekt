<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use Anax\DI\DIMagic;
use PHPUnit\Framework\TestCase;
use Algn\Controller\PostsController;
use Algn\Database\Post;

/**
 * Test the SampleController.
 */
class PostsControllerClassNoDB extends TestCase
{
    // public function __construct() {
    //     parent::__construct();

    //     $this->user = new User();
    //     $this->post = new Post();
    // }
    /**
     * Test the route "index".
     */

    public function testNewAction()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $res = $controller->newAction();


        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }
}
