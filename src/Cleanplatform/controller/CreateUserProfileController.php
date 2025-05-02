<?php
namespace Controller;

use Config\Database;
use Entity\UserProfile;

class CreateUserProfileController {
    private $db;
    private UserProfile $entity;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->entity = new UserProfile($this->db);
    }

    public function execute(int $userId, array $data) : bool {
        // 从当前用户的会话中获取userType(角色)
        $userType = $data['user_type'] ?? $_SESSION['role'];
        
        // 确保userType只能是cleaner或homeowner
        if ($userType !== 'cleaner' && $userType !== 'homeowner') {
            return false; // admin和manager角色暂时不支持个人资料
        }
        
        // 调用正确的方法：createProfile而不是create
        return $this->entity->createProfile($userId, $userType, $data);
    }
}
