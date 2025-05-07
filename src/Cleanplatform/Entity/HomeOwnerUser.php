<?php
// entity/HomeOwnerUser.php
namespace Entity;
require_once __DIR__ . '/User.php';
use Exception;

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
        $sql = 'SELECT c.id, c.username, s.id as service_id, s.name as sname, s.type as stype, s.price, s.description, p.full_name as full, p.bio
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
        // 查询 cleaners 表基本信息及关联的 user_profiles 表资料
        $sql = 'SELECT c.id, c.username, c.status, c.email,
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

            // 更新该清洁工所有服务的浏览量
            $this->updateCleanerProfileViews($cleanerId);

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
        $servicesSql = 'SELECT id FROM cleaner_services WHERE cleaner_id = ?';
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

        // 开始事务
        mysqli_begin_transaction($this->conn);

        try {
            // 添加到收藏夹
            $sql = 'INSERT INTO shortlists (homeowner_id, service_id, added_at) VALUES (?, ?, NOW())';
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $homeownerId, $serviceId);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($ok) {
                // 检查service_stats表中是否已有该服务的记录
                $checkStatsSql = 'SELECT service_id FROM service_stats WHERE service_id = ? LIMIT 1';
                $checkStatsStmt = mysqli_prepare($this->conn, $checkStatsSql);
                mysqli_stmt_bind_param($checkStatsStmt, 'i', $serviceId);
                mysqli_stmt_execute($checkStatsStmt);
                mysqli_stmt_store_result($checkStatsStmt);
                $statsExists = mysqli_stmt_num_rows($checkStatsStmt) > 0;
                mysqli_stmt_close($checkStatsStmt);

                if ($statsExists) {
                    // 更新现有记录的shortlist_count
                    $updateStatsSql = 'UPDATE service_stats SET shortlist_count = shortlist_count + 1 WHERE service_id = ?';
                    $updateStatsStmt = mysqli_prepare($this->conn, $updateStatsSql);
                    mysqli_stmt_bind_param($updateStatsStmt, 'i', $serviceId);
                    $statsOk = mysqli_stmt_execute($updateStatsStmt);
                    mysqli_stmt_close($updateStatsStmt);
                } else {
                    // 创建新记录
                    $insertStatsSql = 'INSERT INTO service_stats (service_id, shortlist_count, view_count) VALUES (?, 1, 0)';
                    $insertStatsStmt = mysqli_prepare($this->conn, $insertStatsSql);
                    mysqli_stmt_bind_param($insertStatsStmt, 'i', $serviceId);
                    $statsOk = mysqli_stmt_execute($insertStatsStmt);
                    mysqli_stmt_close($insertStatsStmt);
                }

                if (!$statsOk) {
                    // 如果更新统计失败，回滚事务
                    mysqli_rollback($this->conn);
                    return false;
                }
            }

            // 提交事务
            mysqli_commit($this->conn);
            return $ok;
        } catch (Exception $e) {
            // 发生异常时回滚事务
            mysqli_rollback($this->conn);
            return false;
        }
    }

    /** 查看我的收藏 */
    public function viewShortlist(int $homeownerId): array {
        $sql = 'SELECT s.id as shortlist_id, cs.id as service_id, cs.name, cs.type, cs.price, cs.cleaner_id'
             . ' FROM shortlists s'
             . ' JOIN cleaner_services cs ON s.service_id = cs.id'
             . ' WHERE s.homeowner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $homeownerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $shortlist_id, $service_id, $name, $type, $price, $cleaner_id);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('shortlist_id', 'service_id', 'name', 'type', 'price', 'cleaner_id');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 搜索收藏 */
    public function searchShortlist(int $homeownerId, string $keyword): array {
        $like = "%$keyword%";
        $sql = 'SELECT s.id as shortlist_id, cs.id as service_id, cs.name, cs.type, cs.price, cs.cleaner_id'
             . ' FROM shortlists s'
             . ' JOIN cleaner_services cs ON s.service_id = cs.id'
             . ' WHERE s.homeowner_id = ? AND cs.name LIKE ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $homeownerId, $like);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $shortlist_id, $service_id, $name, $type, $price, $cleaner_id);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('shortlist_id', 'service_id', 'name', 'type', 'price', 'cleaner_id');
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