<?php
namespace Controller;

use Entity\PlatformManager;

class GenerateWeeklyReportController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(string $start, string $end) : array {
        return $this->entity->executeGenerateWeeklyReport($start, $end);
    }
}
