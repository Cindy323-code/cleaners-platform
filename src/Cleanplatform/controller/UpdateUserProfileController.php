<?php
namespace Controller;

use Entity\UserProfile;

class UpdateUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId, array $fields) : bool {
        // 获取用户类型
        $userType = $_SESSION['role'] ?? 'homeowner';
        
        // 调用entity中的execute方法
        return $this->entity->execute($userId, $userType, $fields);
    }
}
