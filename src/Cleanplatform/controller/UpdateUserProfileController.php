<?php
namespace Controller;

        use Config\Database;
use Entity\UserProfile;

        class UpdateUserProfileController {
            private $db;
    private UserProfile $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new UserProfile($this->db);
            }

            public function execute(int $userId, array $fields) : bool {
        return $this->entity->update($userId, $fields);
            }
        }
