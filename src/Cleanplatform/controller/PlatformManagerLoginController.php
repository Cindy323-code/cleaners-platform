<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class PlatformManagerLoginController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(string $username, string $password) : ?array {
        return (new PlatformManager(Database::getConnection()))->login($username,$password);
            }
        }
