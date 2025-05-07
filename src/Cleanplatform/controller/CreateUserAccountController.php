<?php
namespace Controller;

use Entity\User;
use Entity\AdminUser;

class CreateUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = new AdminUser();
    }

    public function execute(array $data) : bool {
        // 使用entity的executeCreate方法创建用户
        return $this->entity->executeCreate($data);
    }
}
