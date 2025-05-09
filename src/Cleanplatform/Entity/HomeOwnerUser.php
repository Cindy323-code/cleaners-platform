<?php
// entity/HomeOwnerUser.php
namespace Entity;
require_once __DIR__ . '/User.php';
use Exception;

class HomeOwnerUser extends User {
    // 使用基类中已定义的$tableName = 'users'

    /**
     * 执行查看收藏操作，对应ViewShortlistController
     * @param int $userId
     * @return array
     */
    public function executeViewShortlist(int $userId): array
    {
        return $this->viewShortlist($userId);
    }
    
    /**
     * 执行搜索收藏操作，对应SearchShortlistController
     * @param int $userId
     * @param string $keyword
     * @return array
     */
    public function executeSearchShortlist(int $userId, string $keyword): array
    {
        return $this->searchShortlist($userId, $keyword);
    }
    
    /**
     * 执行添加到收藏操作，对应AddCleanerToShortlistController
     * @param int $userId
     * @param int $serviceId
     * @return bool
     */
    public function executeAddToShortlist(int $userId, int $serviceId): bool
    {
        return $this->addToShortlist($userId, $serviceId);
    }
    
    /**
     * 执行从收藏中移除操作，对应RemoveFromShortlistController
     * @param int $userId
     * @param int $shortlistId
     * @return bool
     */
    public function executeRemoveFromShortlist(int $userId, int $shortlistId): bool
    {
        return $this->removeFromShortlist($userId, $shortlistId);
    }
    
    /**
     * 执行搜索可用清洁工操作，对应SearchAvailableCleanersController
     * @param array $criteria
     * @return array
     */
    public function executeSearchAvailableCleaners(array $criteria): array
    {
        return $this->searchAvailableCleaners($criteria);
    }
    
    /**
     * 执行查看清洁工档案操作，对应ViewCleanerProfileController
     * @param int $cleanerId
     * @return array|null
     */
    public function executeViewCleanerProfile(int $cleanerId): ?array
    {
        return $this->viewCleanerProfile($cleanerId);
    }

    /**
     * 执行创建用户账户操作，对应CreateUserAccountController
     * @param array $data
     * @return bool
     */
    public function executeCreate(array $data): bool
    {
        return $this->createUser($data);
    }

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

    /** 用户登录 - 从users表查询homeowner角色 */
    public function login(string $username, string $password): ?array
    {
        $sql = 'SELECT id, username, password_hash, role, status
                FROM ' . static::$tableName . ' WHERE username = ? AND role = "homeowner" LIMIT 1';
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
        $sql = 'SELECT u.id, u.username, s.id as service_id, s.name as sname, s.type as stype, s.price, s.description, p.full_name as full, p.bio
                FROM ' . static::$tableName . ' u
                JOIN cleaner_services s ON u.id = s.user_id
                LEFT JOIN user_profiles p ON u.id = p.user_id
                WHERE u.status = "active" AND u.role = "cleaner"';

        $params = [];
        $types = '';

        // 根据不同的搜索条件添加WHERE子句
        if (!empty($criteria['keyword'])) {
            $keyword = '%' . $criteria['keyword'] . '%';
            $sql .= ' AND (s.name LIKE ? OR s.type LIKE ? OR s.description LIKE ? OR u.username LIKE ? OR p.full_name LIKE ?)';
            $types .= 'sssss';
            $params = array_merge($params, [$keyword, $keyword, $keyword, $keyword, $keyword]);
        } elseif (!empty($criteria['service_type'])) {
            // 保留原有的service_type搜索功能
            $sql .= ' AND s.type = ?';
            $types .= 's';
            $params[] = $criteria['service_type'];
        }

        // 添加排序
        $sql .= ' ORDER BY s.price ASC';

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
                'description' => $row['description'],
                'full' => $row['full'],
                'bio' => $row['bio']
            ];
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 查看清洁工详情 */
    public function viewCleanerProfile(int $cleanerId): ?array {
        // 查询用户信息和用户资料
        $sql = 'SELECT u.id, u.username, u.status, u.email,
                p.full_name, p.avatar_url, p.bio, p.availability, p.status as profile_status
                FROM ' . static::$tableName . ' u
                LEFT JOIN user_profiles p ON u.id = p.user_id
                WHERE u.id = ? AND u.role = "cleaner" LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            
            // 更新浏览计数
            $this->updateCleanerProfileViews($cleanerId);
            
            // 获取该清洁工的服务
            $servicesSql = 'SELECT id, name, type, price, description FROM cleaner_services WHERE user_id = ?';
            $servicesStmt = mysqli_prepare($this->conn, $servicesSql);
            mysqli_stmt_bind_param($servicesStmt, 'i', $cleanerId);
            mysqli_stmt_execute($servicesStmt);
            $servicesResult = mysqli_stmt_get_result($servicesStmt);
            
            $services = [];
            while ($serviceRow = mysqli_fetch_assoc($servicesResult)) {
                $services[] = $serviceRow;
            }
            mysqli_stmt_close($servicesStmt);
            
            // 将服务信息添加到结果中
            $row['services'] = $services;
            
            return $row;
        }
        
        mysqli_stmt_close($stmt);
        return null;
    }

    /**
     * 更新清洁工服务的浏览量
     * @param int $cleanerId 清洁工ID
     * @return bool 更新是否成功
     */
    private function updateCleanerProfileViews(int $cleanerId): bool {
        // 获取该清洁工的所有服务ID
        $servicesSql = 'SELECT id FROM cleaner_services WHERE user_id = ?';
        $servicesStmt = mysqli_prepare($this->conn, $servicesSql);
        mysqli_stmt_bind_param($servicesStmt, 'i', $cleanerId);
        mysqli_stmt_execute($servicesStmt);
        $servicesResult = mysqli_stmt_get_result($servicesStmt);

        $success = true;

        while ($service = mysqli_fetch_assoc($servicesResult)) {
            $serviceId = $service['id'];

            // 检查service_stats表中是否已有该服务的记录
            $checkStatsSql = 'SELECT service_id FROM service_stats WHERE service_id = ? LIMIT 1';
            $checkStatsStmt = mysqli_prepare($this->conn, $checkStatsSql);
            mysqli_stmt_bind_param($checkStatsStmt, 'i', $serviceId);
            mysqli_stmt_execute($checkStatsStmt);
            mysqli_stmt_store_result($checkStatsStmt);
            $statsExists = mysqli_stmt_num_rows($checkStatsStmt) > 0;
            mysqli_stmt_close($checkStatsStmt);

            if ($statsExists) {
                // 更新现有记录的view_count
                $updateStatsSql = 'UPDATE service_stats SET view_count = view_count + 1 WHERE service_id = ?';
                $updateStatsStmt = mysqli_prepare($this->conn, $updateStatsSql);
                mysqli_stmt_bind_param($updateStatsStmt, 'i', $serviceId);
                $statsOk = mysqli_stmt_execute($updateStatsStmt);
                mysqli_stmt_close($updateStatsStmt);
            } else {
                // 创建新记录
                $insertStatsSql = 'INSERT INTO service_stats (service_id, view_count, shortlist_count) VALUES (?, 1, 0)';
                $insertStatsStmt = mysqli_prepare($this->conn, $insertStatsSql);
                mysqli_stmt_bind_param($insertStatsStmt, 'i', $serviceId);
                $statsOk = mysqli_stmt_execute($insertStatsStmt);
                mysqli_stmt_close($insertStatsStmt);
            }

            if (!$statsOk) {
                $success = false;
            }
        }

        mysqli_stmt_close($servicesStmt);
        return $success;
    }

    /** 添加至收藏 */
    public function addToShortlist(int $userId, int $serviceId): bool {
        // 检查是否已在收藏中
        $checkSql = 'SELECT id FROM shortlists WHERE user_id = ? AND service_id = ?';
        $checkStmt = mysqli_prepare($this->conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, 'ii', $userId, $serviceId);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        $exists = mysqli_stmt_num_rows($checkStmt) > 0;
        mysqli_stmt_close($checkStmt);
        
        if ($exists) {
            // 已收藏，不需要重复添加
            return true;
        }
        
        // 添加到收藏
        $sql = 'INSERT INTO shortlists (user_id, service_id) VALUES (?, ?)';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $serviceId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($ok) {
            // 更新服务的收藏统计
            $this->updateServiceShortlistCount($serviceId);
        }
        
        return $ok;
    }
    
    // 更新服务的收藏计数
    private function updateServiceShortlistCount(int $serviceId): void {
        // 先检查是否有记录
        $checkSql = 'SELECT service_id FROM service_stats WHERE service_id = ?';
        $checkStmt = mysqli_prepare($this->conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, 'i', $serviceId);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        $exists = mysqli_stmt_num_rows($checkStmt) > 0;
        mysqli_stmt_close($checkStmt);
        
        if ($exists) {
            // 更新现有记录
            $updateSql = 'UPDATE service_stats SET shortlist_count = shortlist_count + 1 WHERE service_id = ?';
            $updateStmt = mysqli_prepare($this->conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, 'i', $serviceId);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
        } else {
            // 创建新记录
            $insertSql = 'INSERT INTO service_stats (service_id, shortlist_count) VALUES (?, 1)';
            $insertStmt = mysqli_prepare($this->conn, $insertSql);
            mysqli_stmt_bind_param($insertStmt, 'i', $serviceId);
            mysqli_stmt_execute($insertStmt);
            mysqli_stmt_close($insertStmt);
        }
    }
    
    public function viewShortlist(int $userId): array {
        $sql = 'SELECT s.id as shortlist_id, cs.id as service_id, cs.name, cs.type, cs.price,
                cs.description, u.id as cleaner_id, u.username as cleaner_name
                FROM shortlists s
                JOIN cleaner_services cs ON s.service_id = cs.id
                JOIN ' . static::$tableName . ' u ON cs.user_id = u.id
                WHERE s.user_id = ? AND u.role = "cleaner"
                ORDER BY s.added_at DESC';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $res = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $res[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $res;
    }
    
    public function searchShortlist(int $userId, string $keyword): array {
        $like = '%' . $keyword . '%';
        $sql = 'SELECT s.id as shortlist_id, cs.id as service_id, cs.name, cs.type, cs.price,
                cs.description, u.id as cleaner_id, u.username as cleaner_name
                FROM shortlists s
                JOIN cleaner_services cs ON s.service_id = cs.id
                JOIN ' . static::$tableName . ' u ON cs.user_id = u.id
                WHERE s.user_id = ? AND u.role = "cleaner" AND 
                (cs.name LIKE ? OR cs.type LIKE ? OR cs.description LIKE ? OR u.username LIKE ?)
                ORDER BY s.added_at DESC';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'issss', $userId, $like, $like, $like, $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $res = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $res[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $res;
    }
    
    public function removeFromShortlist(int $userId, int $shortlistId): bool {
        // 查找服务ID，以便之后更新其统计数据
        $findSql = 'SELECT service_id FROM shortlists WHERE id = ? AND user_id = ?';
        $findStmt = mysqli_prepare($this->conn, $findSql);
        mysqli_stmt_bind_param($findStmt, 'ii', $shortlistId, $userId);
        mysqli_stmt_execute($findStmt);
        mysqli_stmt_bind_result($findStmt, $serviceId);
        $found = mysqli_stmt_fetch($findStmt);
        mysqli_stmt_close($findStmt);
        
        if (!$found) {
            return false;
        }
        
        // 从收藏中移除
        $sql = 'DELETE FROM shortlists WHERE id = ? AND user_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $shortlistId, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($ok) {
            // 更新服务的收藏统计
            $this->decrementServiceShortlistCount($serviceId);
        }
        
        return $ok;
    }
    
    // 减少服务的收藏计数
    private function decrementServiceShortlistCount(int $serviceId): void {
        $sql = 'UPDATE service_stats SET shortlist_count = GREATEST(0, shortlist_count - 1) WHERE service_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serviceId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}