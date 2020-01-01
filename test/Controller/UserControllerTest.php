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
class SampleControllerTest extends TestCase
{
    public function __construct() {
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

        $this->assertEquals($result["username"], $username);
        $this->assertEquals($result["email"], $email);
        $this->assertEquals($result["password"], hash("sha256", $password));

        $this->user->delete($result["id"]);
    }

    public function testRegisterFail() {
        $result = $this->user->register(null, null, null);

        $this->assertIsString($result["err"]);
        $this->assertIsString($result["code"]);
    }

    public function testComments() {
        $username = "test";
        $password = "test";
        $email = "test@test.com";

        $this->user->register($username, $password, $email);

        $user = $this->user->getFromName($username);

        $result = $this->user->comments($user["id"]);

        $this->assertEquals(count($result), 0);

        $this->user->delete($user["id"]);
    }

    public function testScore() {
        $username = "test";
        $password = "test";
        $email = "test@test.com";

        $this->user->register($username, $password, $email);

        $user = $this->user->getFromName($username);

        $result = $this->user->score($user["id"]);

        $this->assertEquals($result, 0);

        $this->user->delete($user["id"]);
    }

    public function testActivity() {
        $username = "test";
        $password = "test";
        $email = "test@test.com";

        $this->user->register($username, $password, $email);

        $user = $this->user->getFromName($username);

        $result = $this->user->activity($user["id"]);

        $this->assertEquals(count($result["comments"]), 0);
        $this->assertEquals(count($result["posts"]), 0);

        $this->user->delete($user["id"]);
    }

    public function testGet() {
        $username = "test";
        $password = "test";
        $email = "test@test.com";

        $this->user->register($username, $password, $email);

        $user = $this->user->getFromName($username);

        $result = $this->user->get($user["id"]);

        $this->assertEquals($result["username"], $username);
        $this->assertEquals($result["email"], $email);
        $this->assertEquals($result["password"], hash("sha256", $password));

        $this->user->delete($user["id"]);
    }

    public function testVerify() {
        $username = "test";
        $password = "test";
        $email = "test@test.com";

        $this->user->register($username, $password, $email);

        $user = $this->user->getFromName($username);

        $result = $this->user->verify($username, $password);

        $this->assertEquals($result["verified"], true);

        $this->user->delete($user["id"]);
    }
}
