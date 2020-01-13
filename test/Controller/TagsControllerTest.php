<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use Anax\DI\DIMagic;
use PHPUnit\Framework\TestCase;
use Algn\Controller\TagsController;
use Algn\Database\Post;

/**
 * Test the SampleController.
 */
class TagsControllerClass extends TestCase
{
    // public function __construct() {
    //     parent::__construct();

    //     $this->user = new User();
    //     $this->post = new Post();
    // }
    /**
     * Test the route "index".
     */
    public function testIndex()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $di->request->setGet("tags", "hola");

        $controller = new TagsController();
        $controller->setDI($di);

        $res = $controller->indexAction();
        $res = $controller->catchAll();

        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }
}
