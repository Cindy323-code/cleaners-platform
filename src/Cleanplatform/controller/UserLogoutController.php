<?php
namespace Controller;

use Entity\User;
use Entity\AdminUser;

class UserLogoutController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'admin']);
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
