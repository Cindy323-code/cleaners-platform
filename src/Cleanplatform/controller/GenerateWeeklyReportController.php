<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class GenerateWeeklyReportController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(string $start, string $end) : array {
        return $this->entity->generateWeeklyReport($start,$end);
            }
        }
