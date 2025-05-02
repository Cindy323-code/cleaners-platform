<?php
namespace Controller;

use Config\Database;
use Entity\AdminUser;
use Entity\CleanerUser;
use Entity\HomeOwnerUser;

// controller/UserLoginController.php
class UserLoginController {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function execute(string $u, string $p): ?array {
        error_log("DBG UserLoginController called: user=$u");    // ★
        
        // 尝试在admin_users表中登录
        $info = (new \Entity\AdminUser($this->db))->login($u, $p);
        
        // 如果admin_users表中没有找到用户，尝试cleaners表
        if (!$info) {
            $info = (new \Entity\CleanerUser($this->db))->login($u, $p);
        }
        
        // 如果cleaners表中也没有找到用户，尝试homeowners表
        if (!$info) {
            $info = (new \Entity\HomeOwnerUser($this->db))->login($u, $p);
        }
        
        error_log('DBG login() returns: '.json_encode($info));   // ★
        return $info;
    }
}

