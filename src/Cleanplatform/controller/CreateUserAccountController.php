<?php
namespace Controller;

        use Config\Database;
use Entity\AdminUser;

        class CreateUserAccountController {
            private $db;
    private AdminUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new AdminUser($this->db);
            }

            public function execute(array $data) : bool {
        return $this->entity->createUser($data);
            }
        }
