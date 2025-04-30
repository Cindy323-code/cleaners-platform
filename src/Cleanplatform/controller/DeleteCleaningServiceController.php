<?php
namespace Controller;

        use Config\Database;
use Entity\CleanerUser;

        class DeleteCleaningServiceController {
            private $db;
    private CleanerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new CleanerUser($this->db);
            }

            public function execute(int $serviceId) : bool {
        return $this->entity->deleteService($serviceId);
            }
        }
