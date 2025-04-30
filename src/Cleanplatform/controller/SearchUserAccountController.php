<?php
namespace Controller;

        use Config\Database;
use Entity\AdminUser;

        class SearchUserAccountController {
            private $db;
    private AdminUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new AdminUser($this->db);
            }

            public function execute(string $keyword) : array {
        return $this->entity->searchUsers($keyword);
            }
        }
