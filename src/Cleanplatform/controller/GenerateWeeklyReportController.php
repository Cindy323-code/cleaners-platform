<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class GenerateWeeklyReportController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(string $start, string $end) : array {
        return $this->entity->executeGenerateWeeklyReport($start, $end);
    }
}
