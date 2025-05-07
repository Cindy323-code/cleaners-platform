<?php
namespace Controller;

use Entity\UserProfile;

class CreateUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId, array $data) : bool {
        // 从当前用户的会话中获取userType(角色)
        $userType = $data['user_type'] ?? $_SESSION['role'];
        
        // 确保userType只能是cleaner或homeowner
        if ($userType !== 'cleaner' && $userType !== 'homeowner') {
            return false; // admin和manager角色暂时不支持个人资料
        }
        
        // 调用与控制器同名的方法
        return $this->entity->execute($userId, $userType, $data);
    }
}
