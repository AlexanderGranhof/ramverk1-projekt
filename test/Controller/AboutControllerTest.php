<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use Anax\DI\DIMagic;
use PHPUnit\Framework\TestCase;
use Algn\Controller\AboutController;
use Algn\Database\Post;

/**
 * Test the SampleController.
 */
class AboutControllerTest extends TestCase
{
    /**
     * Test the route "index".
     */
    public function testIndex()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new AboutController();
        $controller->setDI($di);

        $res = $controller->indexAction();
        $res = $controller->catchAll();

        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }
}
