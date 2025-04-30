<?php
namespace Controller;

        use Config\Database;
use Entity\CleanerUser;

        class UpdateCleaningServiceController {
            private $db;
    private CleanerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new CleanerUser($this->db);
            }

            public function execute(int $serviceId, array $fields) : bool {
        return $this->entity->updateService($serviceId, $fields);
            }
        }
