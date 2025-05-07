<?php
namespace Controller;

use Entity\AdminUser;

class UpdateUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = new AdminUser();
    }

    public function execute(string $username, array $fields) : bool {
        // 使用entity的executeUpdate方法更新用户
        return $this->entity->executeUpdate($username, $fields);
    }
}
