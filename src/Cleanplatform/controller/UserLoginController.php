<?php
namespace Controller;

use Entity\AdminUser;
use Entity\CleanerUser;
use Entity\HomeOwnerUser;

// controller/UserLoginController.php
class UserLoginController {
    
    public function execute(string $u, string $p): ?array {
        error_log("DBG UserLoginController called: user=$u");    // ★
        
        // 尝试在admin_users表中登录
        $info = (new AdminUser())->executeLogin($u, $p);
        
        // 如果admin_users表中没有找到用户，尝试cleaners表
        if (!$info) {
            $info = (new CleanerUser())->executeLogin($u, $p);
        }
        
        // 如果cleaners表中也没有找到用户，尝试homeowners表
        if (!$info) {
            $info = (new HomeOwnerUser())->executeLogin($u, $p);
        }
        
        error_log('DBG login() returns: '.json_encode($info));   // ★
        return $info;
    }
}

