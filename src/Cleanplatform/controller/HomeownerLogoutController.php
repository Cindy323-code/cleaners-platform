<?php
namespace Controller;

use Entity\HomeOwnerUser;

class HomeownerLogoutController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
