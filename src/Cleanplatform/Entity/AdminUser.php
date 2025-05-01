<?php
// entity/AdminUser.php
namespace Entity;
require_once __DIR__ . '/User.php'; 

class AdminUser extends User {
    protected static string $tableName = 'admin_users';

    /** 创建用户账户 */
    public function createUser(array $data): bool {
        $sql = 'INSERT INTO ' . static::$tableName
             . ' (username,password_hash,email,role,status) VALUES (?,?,?,?,?)';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt, 'sssss',
            $data['username'],
            $data['password_hash'],
            $data['email'],
            $data['role'],
            $data['status']
        );
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 查看用户详情 */
    public function viewUser(string $username): ?array {
        $sql = 'SELECT id,username,email,role,status,created_at'
             . ' FROM ' . static::$tableName
             . ' WHERE username = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id, $user, $email, $role, $status, $createdAt
        );
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return compact('id','user','email','role','status','createdAt');
        }
        mysqli_stmt_close($stmt);
        return null;
    }

    /** 更新用户信息 */
    public function updateUser(string $username, array $fields): bool {
        $sets = [];
        $types = '';
        $vals  = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $types .= 's';
            $vals[]  = $val;
        }
        $sql = 'UPDATE ' . static::$tableName
             . ' SET ' . implode(',', $sets)
             . ' WHERE username = ?';
        $types .= 's';
        $vals[] = $username;
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$vals);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 暂停（挂起）用户 */
    public function suspendUser(string $username): bool {
        return $this->updateUser($username, ['status' => 'suspended']);
    }

    /** 搜索用户 */
    public function searchUsers(string $keyword = '', string $role = '', string $status = ''): array {
        // 构建基本SQL查询
        $sql = 'SELECT username, role, email, status FROM ' . static::$tableName;
        $conditions = [];
        $params = [];
        $types = '';
        
        // 添加关键字搜索条件
        if (!empty($keyword)) {
            $like = "%$keyword%";
            $conditions[] = '(username LIKE ? OR email LIKE ?)';
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }
        
        // 添加角色筛选条件
        if (!empty($role)) {
            $conditions[] = 'role = ?';
            $params[] = $role;
            $types .= 's';
        }
        
        // 添加状态筛选条件
        if (!empty($status)) {
            $conditions[] = 'status = ?';
            $params[] = $status;
            $types .= 's';
        }
        
        // 组合WHERE子句
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        
        // 添加排序
        $sql .= ' ORDER BY username ASC';
        
        $stmt = mysqli_prepare($this->conn, $sql);
        
        // 如果有参数，绑定它们
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user, $role, $email, $status);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('user', 'role', 'email', 'status');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }
    
    /** 获取所有用户 */
    public function getAllUsers(): array {
        $sql = 'SELECT id, username, email, role, status, created_at'
             . ' FROM ' . static::$tableName
             . ' ORDER BY created_at DESC';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id, $user, $email, $role, $status, $createdAt
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id', 'user', 'email', 'role', 'status', 'createdAt');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }
}