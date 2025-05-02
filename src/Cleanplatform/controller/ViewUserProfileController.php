<?php
namespace Controller;

use Config\Database;
use Entity\UserProfile;

class ViewUserProfileController {
    private $db;
    private UserProfile $entity;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->entity = new UserProfile($this->db);
    }

    public function execute(int $userId, string $userRole) : ?array {
        // 将userRole映射到UserProfile类中期望的userType格式
        $userType = $userRole;
        // 确保userType只能是cleaner或homeowner
        if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
            // 如果是admin或manager，暂时不支持资料，返回null
            return null;
        }
        
        // 调用正确的方法：viewProfile而不是read
        return $this->entity->viewProfile($userId, $userType);
    }
}
