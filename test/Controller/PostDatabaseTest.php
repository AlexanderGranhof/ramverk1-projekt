<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;
use Algn\Database\User;
use Algn\Database\Post;
use PDO;
use PDOException;

/**
 * Test the SampleController.
 */
class PostDatabaseTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        $this->user = new User();
        $this->post = new Post();
    }
    /**
     * Test the route "index".
     */
    public function testRegisterAndGetFromName()
    {
        $username = "test";
        $password = "test";
        $email = "test@test.com";

        $this->user->register($username, $password, $email);

        $result = $this->user->getFromName($username);

        $this->post->create($result["id"], "test", "test", "test");

        $all = $this->post->all();

        $top = $this->post->top();
        $rank = $this->post->rank($all[0]["id"]);
        $scre = $this->post->postScore($result["id"], $all[0]["id"]);
        $comment = $this->post->comment($all[0]["id"], $result["id"], "test");
        $comments = $this->post->getComments($all[0]["id"]);
        $topc = $this->post->topComments($all[0]["id"]);
        $owner = $this->post->isOwner($result["id"], $all[0]["id"]);

        $this->assertIsArray($comments);
        $this->assertIsArray($top);
        $this->assertIsArray($all);
        $this->assertEquals(count($all) >= 1, true);
        $this->assertEquals(is_null($rank), false);
        $this->assertEquals(is_null($scre), false);
        $this->assertEquals(is_null($comment), false);
        $this->assertEquals(is_null($topc), false);
        $this->assertEquals(is_null($owner), false);
        $this->assertEquals($result["username"], $username);
        $this->assertEquals($result["email"], $email);
        $this->assertEquals($result["password"], hash("sha256", $password));

        $this->post->deleteComment($comments[0]["id"]);
        $this->post->delete($all[0]["id"]);
        $this->user->delete($result["id"]);
    }
}
