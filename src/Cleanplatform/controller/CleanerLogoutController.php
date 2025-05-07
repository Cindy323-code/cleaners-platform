<?php
namespace Controller;

use Entity\CleanerUser;
use Entity\User;

class CleanerLogoutController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'cleaner']);
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
