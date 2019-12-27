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

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetch();
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

    public function register($username, $password, $email) {
        $password = hash("sha256", $password);

        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
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
}
