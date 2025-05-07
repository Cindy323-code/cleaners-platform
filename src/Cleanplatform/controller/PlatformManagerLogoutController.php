<?php
namespace Controller;

use Entity\PlatformManager;

class PlatformManagerLogoutController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
