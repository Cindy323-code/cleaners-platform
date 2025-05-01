<?php
namespace Controller;

use Config\Database;
use Entity\AdminUser;

class ViewUserAccountController {
    private $db;
    private AdminUser $entity;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->entity = new AdminUser($this->db);
    }

    public function execute(string $username) : ?array {
        // 先在 admin_users 表中查询
        $result = $this->entity->viewUser($username);
        if ($result) {
            return $result;
        }
        
        // 如果没找到，在 cleaners 表中查询
        $db = Database::getConnection();
        $sql = 'SELECT id,username,email,role,status,created_at FROM cleaners WHERE username = ? LIMIT 1';
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $user, $email, $role, $status, $createdAt);
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return compact('id', 'user', 'email', 'role', 'status', 'createdAt');
        }
        mysqli_stmt_close($stmt);
        
        // 如果还没找到，在 homeowners 表中查询
        $sql = 'SELECT id,username,email,role,status,created_at FROM homeowners WHERE username = ? LIMIT 1';
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $user, $email, $role, $status, $createdAt);
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return compact('id', 'user', 'email', 'role', 'status', 'createdAt');
        }
        mysqli_stmt_close($stmt);
        
        return null;
    }
    
    public function getAllUsers() : array {
        return $this->entity->getAllUsers();
    }
}
