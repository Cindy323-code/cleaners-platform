<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class GenerateDailyReportController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(string $date) : array {
        return $this->entity->executeGenerateDailyReport($date);
    }
}
