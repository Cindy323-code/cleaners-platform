<?php
namespace Controller;

use Entity\User;
use Entity\AdminUser;

class UserLogoutController {
    private User $entity;

    public function __construct() {
        // 使用任意用户实体，因为logout方法是在基类中
        $this->entity = new AdminUser();
    }

    public function execute() : void {
        $this->entity->executeLogout();
    }
}
