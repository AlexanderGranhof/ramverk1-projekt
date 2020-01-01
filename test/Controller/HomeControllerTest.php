<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use Anax\DI\DIMagic;
use PHPUnit\Framework\TestCase;
use Algn\Controller\HomeController;
use Algn\Database\Post;

/**
 * Test the SampleController.
 */
class HomeControllerClass extends TestCase
{
    // public function __construct() {
    //     parent::__construct();

    //     $this->user = new User();
    //     $this->post = new Post();
    // }
    /**
     * Test the route "index".
     */
    public function testIndex() {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new HomeController();
        $controller->setDI($di);

        $res = $controller->indexAction();

        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }
}
