<?php

namespace Algn\Database;

// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// ini_set("display_startup_errors", 1);

use PDO;
use PDOException;

class User extends Database
{
    public function __construct() {
        parent::__construct();
    }

    public function all() {
        $stmt = $this->db->prepare("SELECT id, username, created, email FROM users");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetch();
    }

    public function activity($uid) {
        $stmt = $this->db->prepare("SELECT posts.*, posts.id AS `post_id`, users.*, post_votes.score FROM posts INNER JOIN users on users.id = posts.user_id LEFT OUTER JOIN post_votes ON post_votes.post_id = posts.id WHERE posts.user_id = :uid AND posts.deleted = 0");
        $stmt->execute(["uid" => $uid]);

        $posts = $stmt->fetchAll();

        $stmt = $this->db->prepare("SELECT comments.*, posts.title AS `post_title`, comment_votes.score AS `comment_score`, posts.id AS `post_id`, post_votes.score AS `post_score`, users.username AS `post_username`, users.id AS `post_user_id`, posts.created AS `post_created` FROM comments INNER JOIN posts ON posts.id = comments.post_id LEFT OUTER JOIN comment_votes ON comment_id = comments.id LEFT OUTER JOIN post_votes ON posts.id = post_votes.post_id INNER JOIN users ON posts.user_id = users.id WHERE comments.user_id = :uid");
        $stmt->execute(["uid" => $uid]);

        $comments = $stmt->fetchAll();

        return ["comments" => $comments, "posts" => $posts];
    }

    public function score($uid) {
        // $stmt = $this->db->prepare("SELECT (SELECT SUM(score) FROM comment_votes WHERE user_id = :uid) + (SELECT SUM(score) FROM post_votes WHERE user_id = :uid2) AS sum_score;");
        $stmt = $this->db->prepare("SELECT (SELECT COALESCE(SUM(score), 0) FROM comment_votes WHERE user_id = :uid1) + (SELECT COALESCE(COUNT(id), 0) FROM posts WHERE user_id = :uid2) + (SELECT COALESCE(COUNT(id), 0) FROM comments WHERE user_id = :uid3) + (SELECT COALESCE(SUM(comment_votes.score), 0) * 2 FROM comments LEFT OUTER JOIN comment_votes ON comment_votes.comment_id = comments.id WHERE comments.answer = 1 AND comments.user_id = :uid4) + (SELECT COALESCE(SUM(score), 0) FROM post_votes WHERE user_id = :uid5) AS sum_score;");
        $stmt->execute(["uid1" => $uid, "uid2" => $uid, "uid3" => $uid, "uid4" => $uid, "uid5" => $uid]);

        $res = $stmt->fetch();

        return intval($res["sum_score"]);
    }

    public function comments($id) {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE user_id = :id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetchAll();
    }

    public function getFromName($name) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :name LIMIT 1");
        $stmt->execute(["name" => $name]);

        return $stmt->fetch();
    }

    public function grantMod($userID) {
        $stmt = $this->db->prepare("UPDATE users SET moderator = 1 WHERE id = :uid");
        $stmt->execute(["uid" => $userID]);
    }

    public function register($username, $password, $email) {
        $password = hash("sha256", $password);

        $isAdmin = $username == "admin" ? 1 : 0;

        try {
            if ($isAdmin) {
                $stmt = $this->db->prepare("INSERT INTO users (username, password, email, moderator) VALUES (:username, :password, :email, 1)");
            } else {
                $stmt = $this->db->prepare("INSERT INTO users (username, password, email, moderator) VALUES (:username, :password, :email, 0)");
            }

            $stmt->execute(["username" => $username, "password" => $password, "email" => $email]);

            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username AND password = :password AND email = :email");
            $stmt->execute(["username" => $username, "password" => $password, "email" => $email]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            return [
                "err" => $e->getMessage(),
                "code" => $e->getCode()
            ];
        }

        return false;
    }

    public function verify($username, $password) {
        $password = hash("sha256", $password);

        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username AND password = :password");
            $stmt->execute(["username" => $username, "password" => $password]);

            $res = $stmt->fetch();
            
            $id = !!$res ? $res["id"] : null;
    
            return [
                "verified" => !!$res,
                "id" => $id
            ];

        } catch (PDOException $e) {
            return [
                "err" => $e->getMessage(),
                "code" => $e->getCode(),
                "verified" => false
            ];
        }

        return [
            "verified" => false
        ];
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(["id" => $id]);
    }
}
