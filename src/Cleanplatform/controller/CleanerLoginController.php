<?php
namespace Controller;

use Config\Database;
use Entity\CleanerUser;

    class CleanerLoginController {
        private $db;
        private CleanerUser $entity;

        public function __construct() {
            $this->db = Database::getConnection();
            $this->entity = new CleanerUser($this->db);
        }

        public function execute(string $username, string $password) : ?array {
            return (new CleanerUser(Database::getConnection()))->login($username,$password);
        }
    }
