<?php
// entity/AdminUser.php
namespace Entity;
require_once __DIR__ . '/User.php'; 

class AdminUser extends User {
    // 使用基类中已定义的$tableName = 'users'

    /**
     * 执行查看用户操作，对应ViewUserAccountController
     * @param string $username
     * @return array|null
     */
    public function execute(string $username): ?array 
    {
        return $this->viewUser($username);
    }
    
    /**
     * 执行获取所有用户操作，对应ViewUserAccountController的getAllUsers方法
     * @return array
     */
    public function executeGetAll(): array
    {
        return $this->getAllUsers();
    }
    
    /**
     * 执行创建用户操作，对应CreateUserAccountController
     * @param array $data
     * @return bool
     */
    public function executeCreate(array $data): bool
    {
        return $this->createUser($data);
    }
    
    /**
     * 执行更新用户操作，对应UpdateUserAccountController
     * @param string $username
     * @param array $fields
     * @return bool
     */
    public function executeUpdate(string $username, array $fields): bool
    {
        return $this->updateUser($username, $fields);
    }
    
    /**
     * 执行暂停用户操作，对应SuspendUserAccountController
     * @param string $username
     * @return bool
     */
    public function executeSuspend(string $username): bool
    {
        return $this->suspendUser($username);
    }
    
    /**
     * 执行搜索用户操作，对应SearchUserAccountController
     * @param string $keyword
     * @param string $role
     * @param string $status
     * @return array
     */
    public function executeSearch(string $keyword = '', string $role = '', string $status = ''): array
    {
        return $this->searchUsers($keyword, $role, $status);
    }

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
        $sql = 'SELECT u.id, u.username, u.email, u.role, u.status, u.created_at,'
             . ' up.full_name, up.avatar_url, up.bio, up.availability, up.updated_at as profile_updated_at'
             . ' FROM ' . static::$tableName . ' u'
             . ' LEFT JOIN user_profiles up ON u.id = up.user_id'
             . ' WHERE u.username = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id, $db_username, $email, $role, $status, $createdAt,
            $full_name, $avatar_url, $bio, $availability, $profile_updated_at
        );
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return [
                'id' => $id,
                'user' => $db_username, // Use $db_username to avoid conflict with param $username
                'email' => $email,
                'role' => $role,
                'status' => $status,
                'createdAt' => $createdAt,
                'full_name' => $full_name,
                'avatar_url' => $avatar_url,
                'bio' => $bio,
                'availability' => $availability,
                'profile_updated_at' => $profile_updated_at
            ];
        }
        mysqli_stmt_close($stmt);
        return null;
    }

    /** 更新用户信息 */
    public function updateUser(string $username, array $fields): bool {
        // 使用统一的users表
        $userTable = static::$tableName;
        
        // 检查用户是否存在
        $sql = 'SELECT id, role, status FROM ' . $userTable . ' WHERE username = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $role, $status);
        $userExists = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        // 用户不存在
        if (!$userExists) {
            return false;
        }
        
        // 执行更新
        $sets = [];
        $types = '';
        $vals = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $types .= 's';
            $vals[] = $val;
        }
        
        $sql = "UPDATE $userTable SET " . implode(',', $sets) . ' WHERE username = ?';
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
        $sql = "UPDATE " . static::$tableName . " SET status = 'suspended' WHERE username = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        $ok = mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $ok && $affected > 0;
    }

    /** 搜索用户 */
    public function searchUsers(string $keyword = '', string $role = '', string $status = ''): array {
        $results = [];
        $this->searchFromTable(static::$tableName, $keyword, $role, $status, $results);
        return $results;
    }

    private function searchFromTable(string $tableName, string $keyword, string $role, string $status, array &$results): void {
        $conditions = [];
        $params = [];
        $types = '';
        
        if (!empty($keyword)) {
            $likeKeyword = "%$keyword%";
            $conditions[] = "(username LIKE ? OR email LIKE ?)";
            $params[] = $likeKeyword;
            $params[] = $likeKeyword;
            $types .= 'ss';
        }
        
        if (!empty($role)) {
            $conditions[] = "role = ?";
            $params[] = $role;
            $types .= 's';
        }
        
        if (!empty($status)) {
            $conditions[] = "status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $whereClause = !empty($conditions) ? " WHERE " . implode(' AND ', $conditions) : '';
        
        $sql = "SELECT id, username, email, role, status, created_at FROM $tableName" . $whereClause . " ORDER BY created_at DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $username, $email, $role, $status, $createdAt);
        
        while (mysqli_stmt_fetch($stmt)) {
            $results[] = [
                'id' => $id,
                'user' => $username,
                'email' => $email,
                'role' => $role,
                'status' => $status,
                'createdAt' => $createdAt,
                'table' => $tableName // 添加表名以便区分
            ];
        }
        
        mysqli_stmt_close($stmt);
    }

    public function getAllUsers(): array {
        $sql = "SELECT id, username, email, role, status, created_at FROM " . static::$tableName . " ORDER BY created_at DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $username, $email, $role, $status, $createdAt);
        
        $results = [];
        while (mysqli_stmt_fetch($stmt)) {
            $results[] = [
                'id' => $id,
                'user' => $username,
                'email' => $email,
                'role' => $role,
                'status' => $status,
                'createdAt' => $createdAt
            ];
        }
        
        mysqli_stmt_close($stmt);
        return $results;
    }
}