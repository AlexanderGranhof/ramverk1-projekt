<?php

namespace Algn\Database;
use \PDO;


class Database 
{

    public function __construct() {
        $options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];

        if ($_SERVER["SERVER_NAME"] === "www.student.bth.se") {
            $this->db = new PDO("mysql:host=blu-ray.student.bth.se;dbname=algn18", "algn18", "HY8r5EbwyKBP", $options);
        } else {
            $this->db = new PDO("mysql:host=127.0.0.1;dbname=test", "root", "root", $options);
        }

    }
}