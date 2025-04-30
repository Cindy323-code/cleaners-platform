<?php
namespace Controller;

        use Config\Database;
use Entity\CleanerUser;

        class CreateCleaningServiceController {
            private $db;
    private CleanerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new CleanerUser($this->db);
            }

            public function execute(array $data) : bool {
        return $this->entity->createService($data);
            }
        }
