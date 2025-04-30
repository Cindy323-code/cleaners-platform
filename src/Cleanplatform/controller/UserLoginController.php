<?php
namespace Controller;

use Config\Database;
use Entity\AdminUser;

// controller/UserLoginController.php
class UserLoginController {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }
    public function execute(string $u, string $p): ?array
{
    error_log("DBG UserLoginController called: user=$u");    // ★
    $info = (new \Entity\AdminUser($this->db))->login($u, $p);
    error_log('DBG login() returns: '.json_encode($info));   // ★
    return $info;
}

}

