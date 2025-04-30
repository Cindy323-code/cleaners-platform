<?php
namespace Controller;

        use Config\Database;
use Entity\CleanerUser;

        class SearchCleaningServicesController {
            private $db;
    private CleanerUser $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new CleanerUser($this->db);
            }

            public function execute(int $cleanerId, string $keyword) : array {
        return $this->entity->searchServices($cleanerId, $keyword);
            }
        }
