<?php
namespace Controller;

        use Config\Database;
use Entity\HomeOwnerUser;

        class SearchShortlistController {
            private $db;
    private HomeOwnerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new HomeOwnerUser($this->db);
            }

            public function execute(int $homeownerId, string $keyword) : array {
        return $this->entity->searchShortlist($homeownerId,$keyword);
            }
        }
