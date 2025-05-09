<?php
namespace Controller;

use Entity\UserProfile;

class ViewUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(int $userId, string $userRole = null) : ?array {
        // 确保userRole只能是cleaner或homeowner
        if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
            // 如果是admin或manager，暂时不支持资料，返回null
            return null;
        }
        
        // 调用对应的方法，现在只传递userId
        return $this->entity->execute($userId);
    }
}
