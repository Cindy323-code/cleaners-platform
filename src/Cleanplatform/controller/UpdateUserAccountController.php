<?php
namespace Controller;

        use Config\Database;
use Entity\AdminUser;

        class UpdateUserAccountController {
            private $db;
    private AdminUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new AdminUser($this->db);
            }

            public function execute(string $username, array $fields) : bool {
        return $this->entity->updateUser($username, $fields);
            }
        }
