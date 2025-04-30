<?php
namespace Controller;

        use Config\Database;
use Entity\UserProfile;

        class DeactivateUserProfileController {
            private $db;
    private UserProfile $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new UserProfile($this->db);
            }

            public function execute(int $userId) : bool {
        return $this->entity->deactivate($userId);
            }
        }
