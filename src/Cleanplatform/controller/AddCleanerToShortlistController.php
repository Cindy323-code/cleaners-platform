<?php
namespace Controller;

        use Config\Database;
use Entity\HomeOwnerUser;

        class AddCleanerToShortlistController {
            private $db;
    private HomeOwnerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new HomeOwnerUser($this->db);
            }

            public function execute(int $homeownerId, int $serviceId) : bool {
        return $this->entity->addToShortlist($homeownerId,$serviceId);
            }
        }
