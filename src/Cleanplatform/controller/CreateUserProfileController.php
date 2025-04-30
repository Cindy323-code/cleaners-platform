<?php
namespace Controller;

        use Config\Database;
use Entity\UserProfile;

        class CreateUserProfileController {
            private $db;
    private UserProfile $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new UserProfile($this->db);
            }

            public function execute(int $userId, array $data) : bool {
        return $this->entity->create($userId, $data);
            }
        }
