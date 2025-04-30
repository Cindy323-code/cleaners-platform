<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class GenerateDailyReportController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(string $date) : array {
        return $this->entity->generateDailyReport($date);
            }
        }
