<?php
namespace Controller;

use Entity\UserProfile;

class UpdateUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId, array $fields) : bool {
        // 确保用户角色是有效的
        $userRole = $_SESSION['role'] ?? '';
        if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
            return false; // admin和manager角色暂时不支持个人资料
        }
        
        // 调用entity中的execute方法，只传递userId和fields
        return $this->entity->execute($userId, $fields);
    }
}
