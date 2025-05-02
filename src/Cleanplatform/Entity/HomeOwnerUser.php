<?php
// entity/HomeOwnerUser.php
namespace Entity;
require_once __DIR__ . '/User.php'; 

class HomeOwnerUser extends User {
    protected static string $tableName = 'homeowners';

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

    /** 用户登录 - 从homeowners表查询 */
    public function login(string $username, string $password): ?array
    {
        $sql = 'SELECT id, username, password_hash, role, status
                FROM homeowners WHERE username = ? LIMIT 1';
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

    /** 搜索可用清洁工 */
    public function searchAvailableCleaners(array $criteria): array {
        // 准备SQL查询
        $sql = 'SELECT c.id, c.username, s.id as service_id, s.name as sname, s.type as stype, s.price, p.full_name as full, p.bio
                FROM cleaners c
                JOIN cleaner_services s ON c.id = s.cleaner_id
                LEFT JOIN user_profiles p ON c.id = p.user_id AND p.user_type = "cleaner"
                WHERE c.status = "active"';
        
        $params = [];
        $types = '';
        
        // 根据不同的搜索条件添加WHERE子句
        if (!empty($criteria['keyword'])) {
            $keyword = '%' . $criteria['keyword'] . '%';
            $sql .= ' AND (s.name LIKE ? OR s.type LIKE ? OR s.description LIKE ? OR c.username LIKE ? OR p.full_name LIKE ?)';
            $types .= 'sssss';
            $params = array_merge($params, [$keyword, $keyword, $keyword, $keyword, $keyword]);
        } elseif (!empty($criteria['service_type'])) {
            // 保留原有的service_type搜索功能
            $sql .= ' AND s.type = ?';
            $types .= 's';
            $params[] = $criteria['service_type'];
        }
        
        // 添加排序
        $sql .= ' ORDER BY c.rating DESC, s.price ASC';
        
        // 准备和执行查询
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // 获取结果
        $res = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $res[] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'service_id' => $row['service_id'],
                'sname' => $row['sname'],
                'stype' => $row['stype'],
                'price' => $row['price'],
                'full' => $row['full'],
                'bio' => $row['bio']
            ];
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 查看清洁工详情 */
    public function viewCleanerProfile(int $cleanerId): ?array {
        // 查询 cleaners 表基本信息及关联的 user_profiles 表资料
        $sql = 'SELECT c.id, c.username, c.rating, c.status, c.email,
                p.full_name, p.avatar_url, p.bio, p.availability, p.status as profile_status
                FROM cleaners c
                LEFT JOIN user_profiles p ON c.id = p.user_id AND p.user_type = "cleaner"
                WHERE c.id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            // 为了前端代码兼容性，添加一些字段映射
            $row['full'] = $row['full_name'] ?? '';
            $row['bio'] = $row['bio'] ?? '';
            return $row;
        }
        
        mysqli_stmt_close($stmt);
        return null;
    }

    /** 添加至收藏 */
    public function addToShortlist(int $homeownerId, int $serviceId): bool {
        // 首先检查服务ID是否存在
        $checkSql = 'SELECT id FROM cleaner_services WHERE id = ? LIMIT 1';
        $checkStmt = mysqli_prepare($this->conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, 'i', $serviceId);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        $exists = mysqli_stmt_num_rows($checkStmt) > 0;
        mysqli_stmt_close($checkStmt);
        
        // 如果服务ID不存在，返回false
        if (!$exists) {
            return false;
        }
        
        // 检查是否已经添加到收藏夹
        $dupeSql = 'SELECT id FROM shortlists WHERE homeowner_id = ? AND service_id = ? LIMIT 1';
        $dupeStmt = mysqli_prepare($this->conn, $dupeSql);
        mysqli_stmt_bind_param($dupeStmt, 'ii', $homeownerId, $serviceId);
        mysqli_stmt_execute($dupeStmt);
        mysqli_stmt_store_result($dupeStmt);
        $isDuplicate = mysqli_stmt_num_rows($dupeStmt) > 0;
        mysqli_stmt_close($dupeStmt);
        
        // 如果已经在收藏夹中，返回true（视为成功添加）
        if ($isDuplicate) {
            return true;
        }
        
        // 添加到收藏夹
        $sql = 'INSERT INTO shortlists (homeowner_id, service_id, added_at) VALUES (?, ?, NOW())';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $homeownerId, $serviceId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 查看我的收藏 */
    public function viewShortlist(int $homeownerId): array {
        $sql = 'SELECT s.id,cs.name,cs.type,cs.price'
             . ' FROM shortlists s'
             . ' JOIN cleaner_services cs ON s.service_id = cs.id'
             . ' WHERE s.homeowner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $homeownerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$id,$name,$type,$price);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','type','price');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 搜索收藏 */
    public function searchShortlist(int $homeownerId, string $keyword): array {
        $like = "%$keyword%";
        $sql = 'SELECT s.id,cs.name,cs.type,cs.price'
             . ' FROM shortlists s'
             . ' JOIN cleaner_services cs ON s.service_id = cs.id'
             . ' WHERE s.homeowner_id = ? AND cs.name LIKE ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $homeownerId, $like);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$id,$name,$type,$price);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','type','price');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 从收藏中删除 */
    public function removeFromShortlist(int $homeownerId, int $shortlistId): bool {
        $sql = 'DELETE FROM shortlists WHERE id = ? AND homeowner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $shortlistId, $homeownerId);
        $ok = mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $ok && $affected > 0;
    }
}