<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class GenerateMonthlyReportController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(int $year, int $month) : array {
        return $this->entity->executeGenerateMonthlyReport($year, $month);
    }
}
