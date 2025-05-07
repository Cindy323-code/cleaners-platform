<?php
namespace Controller;

use Entity\PlatformManager;

class GenerateDailyReportController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(string $date) : array {
        return $this->entity->executeGenerateDailyReport($date);
    }
}
