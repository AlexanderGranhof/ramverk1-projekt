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
class PostsControllerClass extends TestCase
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

        $controller = new PostsController();
        $controller->setDI($di);

        $di->request->setGet("tags", "a,b,c");

        $res = $controller->indexAction();


        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }

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

    public function testNewActionPost()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $di->request->setPost("tags", "a,b,c");

        $res = $controller->newActionPost();


        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }

    public function testNewActionPostNoTags()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $res = $controller->newActionPost();


        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }

    public function testCommentActionPost()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $res = $controller->commentActionPost();


        $this->assertIsString($res);
    }

    public function testCommentVote()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $di->request->setPost("score", "1");
        $di->request->setPost("id", "1");


        $res = $controller->commentVoteActionPost();


        $this->assertIsString($res);
    }

    public function testPostVote()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $di->request->setPost("score", "1");
        $di->request->setPost("id", "1");


        $res = $controller->postVoteActionPost();


        $this->assertIsString($res);
    }

    public function testcommentAnswerActionPost()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $di->request->setPost("score", "1");
        $di->request->setPost("id", "1");


        $res = $controller->commentAnswerActionPost();


        $this->assertIsString($res);
    }

    public function testcatchAll()
    {
        global $di;

        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        $controller = new PostsController();
        $controller->setDI($di);

        $di->request->setPost("score", "1");
        $di->request->setPost("id", "1");


        $res = $controller->indexActionDelete();
        $res = $controller->newActionPost();
        $res = $controller->postVoteActionPost();
        $res = $controller->commentActionDelete();
        $res = $controller->catchAll("1");


        $this->assertIsObject($res);
        $this->assertInstanceOf("Anax\Response\Response", $res);
        $this->assertInstanceOf("Anax\Response\ResponseUtility", $res);
    }
}
