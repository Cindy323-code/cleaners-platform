<?php
namespace Controller;

use Entity\PlatformManager;

class GenerateMonthlyReportController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(int $year, int $month) : array {
        return $this->entity->executeGenerateMonthlyReport($year, $month);
    }
}
