<?php
namespace Controller;

        use Config\Database;
use Entity\UserProfile;

        class SearchUserProfileController {
            private $db;
    private UserProfile $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new UserProfile($this->db);
            }

            public function execute(array $criteria) : array {
        return $this->entity->search($criteria);
            }
        }
