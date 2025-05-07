<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class PlatformManagerLogoutController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
