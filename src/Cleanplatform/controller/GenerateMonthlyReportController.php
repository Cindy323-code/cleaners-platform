<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class GenerateMonthlyReportController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(int $year, int $month) : array {
        return $this->entity->generateMonthlyReport($year,$month);
            }
        }
