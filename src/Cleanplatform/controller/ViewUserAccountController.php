<?php
namespace Controller;

use Entity\AdminUser;
use Entity\User;

class ViewUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'admin']);
    }

    public function execute(string $username) : ?array {
        // 使用entity的execute方法查询用户
        return $this->entity->execute($username);
    }
    
    public function getAllUsers() : array {
        // 使用entity的executeGetAll方法获取所有用户
        return $this->entity->executeGetAll();
    }
}
