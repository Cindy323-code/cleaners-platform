<?php
namespace Controller;

use Entity\UserProfile;

class DeactivateUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId) : bool {
        // 获取用户类型
        $userType = $_SESSION['role'] ?? 'homeowner';
        
        // 调用entity中的相应方法
        return $this->entity->executeDeactivate($userId, $userType);
    }
}
