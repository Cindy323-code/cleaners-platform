<?php
namespace Controller;

use Entity\CleanerUser;

class CleanerLogoutController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
