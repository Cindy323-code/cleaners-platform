<?php
namespace Controller;

use Entity\UserProfile;

class ViewUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = new UserProfile();
    }

    public function execute(int $userId, string $userRole) : ?array {
        // 将userRole映射到UserProfile类中期望的userType格式
        $userType = $userRole;
        // 确保userType只能是cleaner或homeowner
        if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
            // 如果是admin或manager，暂时不支持资料，返回null
            return null;
        }
        
        // 调用对应的方法
        return $this->entity->execute($userId, $userType);
    }
}
