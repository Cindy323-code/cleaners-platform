<?php
namespace Controller;

        use Config\Database;
use Entity\HomeOwnerUser;

        class SearchAvailableCleanersController {
            private $db;
    private HomeOwnerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new HomeOwnerUser($this->db);
            }

            public function execute(array $criteria) : array {
        return $this->entity->searchAvailableCleaners($criteria);
            }
        }
