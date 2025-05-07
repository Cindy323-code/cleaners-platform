<?php
namespace Controller;

use Entity\AdminUser;
use Entity\User;

class SuspendUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'admin']);
    }

    public function execute(string $username) : bool {
        // 使用entity的executeSuspend方法暂停用户
        return $this->entity->executeSuspend($username);
    }
}
