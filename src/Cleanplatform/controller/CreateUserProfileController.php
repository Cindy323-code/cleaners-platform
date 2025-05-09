<?php
namespace Controller;

use Entity\UserProfile;

class CreateUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId, array $data) : bool {
        // 确保用户角色只能是cleaner或homeowner
        $userRole = $_SESSION['role'] ?? '';
        if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
            return false; // admin和manager角色暂时不支持个人资料
        }
        
        // 调用与控制器同名的方法，只传递userId和data
        return $this->entity->execute($userId, $data);
    }
}
