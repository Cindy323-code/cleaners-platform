<?php
namespace Controller;

use Entity\UserProfile;

class DeactivateUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId) : bool {
        // 确保用户角色是有效的
        $userRole = $_SESSION['role'] ?? '';
        if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
            return false; // admin和manager角色暂时不支持个人资料
        }
        
        // 调用entity中的相应方法，只传递userId
        return $this->entity->executeDeactivate($userId);
    }
}
