<?php
// entity/CleanerUser.php
namespace Entity;
require_once __DIR__ . '/User.php'; 

class CleanerUser extends User {
    protected static string $tableName = 'cleaners';

    /** 创建用户账户 */
    public function createUser(array $data): bool {
        $sql = 'INSERT INTO ' . static::$tableName
             . ' (username, password_hash, email, role, status) VALUES (?, ?, ?, ?, ?)';
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

    /** 用户登录 - 从cleaners表查询 */
    public function login(string $username, string $password): ?array
    {
        $sql = 'SELECT id, username, password_hash, role, status
                FROM cleaners WHERE username = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $user, $hash, $role, $status);

        $result = null;

        if (mysqli_stmt_fetch($stmt)) {
            $verified = password_verify($password, $hash)
                     || $password === $hash
                     || (strlen($hash) === 32 && md5($password) === $hash);

            if ($verified && $status === 'active') {
                $result = [
                    'id'       => $id,
                    'username' => $user,
                    'role'     => $role,
                    'status'   => $status
                ];
            }
        }
        mysqli_stmt_close($stmt);
        return $result;
    }

    /** 创建清洁服务 */
    public function createService(array $data): bool {
        $sql = 'INSERT INTO cleaner_services'
             . ' (cleaner_id,name,type,price,description)'
             . ' VALUES(?,?,?,?,?)';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt, 'issds',
            $data['cleaner_id'],
            $data['name'],
            $data['type'],
            $data['price'],
            $data['description']
        );
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 查看我的所有服务 */
    public function viewServices(int $cleanerId): array {
        $sql = 'SELECT id,name,type,price,description,created_at'
             . ' FROM cleaner_services WHERE cleaner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,$id,$name,$type,$price,$description,$createdAt
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','type','price','description','createdAt');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 更新服务 */
    public function updateService(int $id, array $fields): bool {
        $sets = [];
        $types = '';
        $vals  = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $types .= is_int($val) ? 'i' : 's';
            $vals[]  = $val;
        }
        $sql = 'UPDATE cleaner_services SET '
             . implode(',', $sets)
             . ' WHERE id = ?';
        $types .= 'i';
        $vals[] = $id;
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$vals);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 删除服务 */
    public function deleteService(int $id, int $cleanerId): bool {
        // 修改SQL语句，增加cleanerId作为条件，确保只删除属于该清洁工的服务
        $sql = 'DELETE FROM cleaner_services WHERE id = ? AND cleaner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $id, $cleanerId);
        $ok = mysqli_stmt_execute($stmt);
        // 检查是否有行被影响（真正删除了服务）
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        // 只有当真正删除了服务时才返回true
        return $ok && $affected > 0;
    }

    /** 搜索服务 */
    public function searchServices(int $cleanerId, string $keyword): array {
        $like = "%$keyword%";
        $sql = 'SELECT id,name,type,price,description'
             . ' FROM cleaner_services'
             . ' WHERE cleaner_id = ? AND name LIKE ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $cleanerId, $like);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$id,$name,$type,$price,$description);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','type','price','description');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }
}
