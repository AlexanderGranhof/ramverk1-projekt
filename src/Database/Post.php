<?php

namespace Algn\Database;

use PDO;
use PDOException;

class Post extends Database
{
    public function __construct() {
        parent::__construct();
    }

    public function all() {
        $stmt = $this->db->prepare("SELECT posts.*, users.username, SUM(post_votes.score) AS score FROM posts INNER JOIN users ON posts.user_id = users.id LEFT OUTER JOIN post_votes on post_votes.post_id = posts.id GROUP BY posts.id");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT posts.*, users.username, SUM(post_votes.score) AS `score` FROM posts INNER JOIN users ON posts.user_id = users.id LEFT OUTER JOIN post_votes on post_votes.post_id = posts.id WHERE posts.id = :id GROUP BY posts.id, post_votes.post_id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetch();

        
        $stmt = $this->db->prepare("SELECT posts.*, users.username FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.id = :id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetch();
    }

    public function postScore($uid, $pid) {
        $stmt = $this->db->prepare("SELECT score FROM post_votes WHERE user_id = :uid AND post_id = :pid");
        $stmt->execute(["uid" => $uid, "pid" => $pid]);

        return $stmt->fetch();
    }

    public function userUpvotedComments($uid, $pid) {
        $stmt = $this->db->prepare("SELECT comment_votes.comment_id, comment_votes.score FROM comment_votes INNER JOIN comments WHERE comment_votes.user_id = :uid AND comment_votes.score != 0 AND comments.post_id = :pid");
        $stmt->execute(["uid" => $uid, "pid" => $pid]);

        return $stmt->fetchAll();
    }

    public function setAnswer($cid, $uid) {
        $stmt = $this->db->prepare("SELECT * FROM comments INNER JOIN posts ON comments.post_id = posts.id WHERE comments.id = :cid AND posts.user_id = :uid");
        $stmt->execute(["cid" => $cid, "uid" => $uid]);

        $result = $stmt->fetchAll();

        if (count($result) <= 0) {
            return null;
        }

        $stmt = $this->db->prepare("UPDATE comments SET answer = 1 WHERE id = :cid");
        $stmt->execute(["cid" => $cid]);

        return true;
    }

    public function getComments($id, $sort=null) {
        switch ($sort) {
            case 'date':
                $stmt = $this->db->prepare("SELECT comments.*, users.username, SUM(comment_votes.score) AS `score` FROM comments INNER JOIN users ON users.id = comments.user_id LEFT OUTER JOIN comment_votes on comment_votes.comment_id = comments.id WHERE comments.post_id = :id GROUP BY comment_votes.comment_id, comments.id ORDER BY created DESC");
                break;
            
            default:
                $stmt = $this->db->prepare("SELECT comments.*, users.username, SUM(comment_votes.score) AS `score` FROM comments INNER JOIN users ON users.id = comments.user_id LEFT OUTER JOIN comment_votes on comment_votes.comment_id = comments.id WHERE comments.post_id = :id GROUP BY comment_votes.comment_id, comments.id ORDER BY score DESC");
                break;
        }

        $stmt->execute(["id" => $id]);

        return $stmt->fetchAll();
    }

    public function commentVote($cid, $uid, $score) {
        $score = intval($score);

        if (is_nan($score)) {
            return null;
        }

        if ($score > 1 || $score < -1) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO comment_votes (score, user_id, comment_id) VALUES (:score, :uid, :cid) ON DUPLICATE KEY UPDATE score = :score2");
            $stmt->execute(["score" => $score, "uid" => $uid, "cid" => $cid, "score2" => $score ]);
            
            return true;
        } catch(PDOException $e) {
            var_dump($e->getMessage());
        }

    }

    public function postVote($pid, $uid, $score) {
        $score = intval($score);

        if (is_nan($score)) {
            return null;
        }

        if ($score > 1 || $score < -1) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO post_votes (score, user_id, post_id) VALUES (:score, :uid, :pid) ON DUPLICATE KEY UPDATE score = :score2");
            $stmt->execute(["score" => $score, "uid" => $uid, "pid" => $pid, "score2" => $score ]);
            
            return true;
        } catch(PDOException $e) {
            var_dump($e->getMessage());
        }

    }

    public function isOwner($uid, $pid) {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE user_id = :uid AND id = :pid");
        $stmt->execute(["uid" => $uid, "pid" => $pid]);

        $result = $stmt->fetchAll();

        return !!count($result);
    }

    public function comment($postID, $userID, $text, $reply=null) {
        // var_dump($postID, $userID, $text, $reply);
        if (!$userID || !$postID) {
            return null;
        }

        $stmt = $this->db->prepare("INSERT INTO comments (comment_text, user_id, post_id, comment_reply_id) VALUES (:text, :id, :postID, :reply)");
        $stmt->execute(["text" => $text, "id" => $userID, "postID" => $postID, "reply" => $reply]);


        // CREATE TABLE comments (
        //     id INT AUTO_INCREMENT,
        //     comment_text TEXT,
        //     user_id INT NOT NULL,
        //     FOREIGN KEY (user_id) REFERENCES users(id),
        //     comment_reply_id INT,
        //     FOREIGN KEY (comment_reply_id) REFERENCES comments(id),
            
        //     PRIMARY KEY (id)
        // );

        return true;
    }

    public function create($userID, $title, $content, $tags) {
        try {
            $stmt = $this->db->prepare("INSERT INTO posts (user_id, content, title, tags) VALUES (:userID, :content, :title, :tags)");
            $stmt->execute(["userID" => $userID, "content" => $content, "title" => $title, "tags" => $tags]);
            
            $stmt = $this->db->prepare("SELECT * FROM posts WHERE user_id = :userID AND title = :title AND content = :content");
            $stmt->execute(["userID" => $userID, "content" => $content, "title" => $title]);

            return $stmt->fetch();

        } catch (PDOException $e) {
            return [
                "err" => $e->getMessage(),
                "code" => $e->getCode()
            ];
        }
    }
}
