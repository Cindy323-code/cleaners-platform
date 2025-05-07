<?php
namespace Controller;

use Entity\AdminUser;
use Entity\User;

class UpdateUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'admin']);
    }

    public function execute(string $username, array $fields) : bool {
        // 使用entity的executeUpdate方法更新用户
        return $this->entity->executeUpdate($username, $fields);
    }
}
