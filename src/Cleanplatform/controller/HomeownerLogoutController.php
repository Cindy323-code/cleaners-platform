<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class HomeownerLogoutController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
