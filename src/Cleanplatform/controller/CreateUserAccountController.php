<?php
namespace Controller;

use Config\Database;
use Entity\AdminUser;
use Entity\CleanerUser;
use Entity\HomeOwnerUser;

class CreateUserAccountController {
    private $db;
    private $entity;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function execute(array $data) : bool {
        // 根据角色选择正确的实体类
        switch ($data['role']) {
            case 'admin':
            case 'manager':
                $this->entity = new AdminUser($this->db);
                break;
            case 'cleaner':
                $this->entity = new CleanerUser($this->db);
                break;
            case 'homeowner':
                $this->entity = new HomeOwnerUser($this->db);
                break;
            default:
                // 不支持的角色
                return false;
        }
        
        // 调用相应实体的创建用户方法
        return $this->entity->createUser($data);
    }
}
