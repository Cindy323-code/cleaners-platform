<?php
namespace Controller;

use Entity\AdminUser;

class SuspendUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = new AdminUser();
    }

    public function execute(string $username) : bool {
        // 使用entity的executeSuspend方法暂停用户
        return $this->entity->executeSuspend($username);
    }
}
