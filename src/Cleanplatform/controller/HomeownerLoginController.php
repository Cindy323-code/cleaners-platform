<?php
namespace Controller;

        use Config\Database;
use Entity\HomeOwnerUser;

        class HomeownerLoginController {
            private $db;
    private HomeOwnerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new HomeOwnerUser($this->db);
            }

            public function execute(string $username, string $password) : ?array {
        return (new HomeOwnerUser(Database::getConnection()))->login($username,$password);
            }
        }
