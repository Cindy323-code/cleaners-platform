<?php
namespace Entity;

use mysqli;
use Config\Database;
require_once __DIR__ . '/User.php'; 
class UserProfile
{
    private mysqli $conn;

    // Static factory method to get UserProfile instance
    public static function getInstance(): UserProfile {
        return new self();
    }

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * 统一执行方法，根据参数数量和类型分发到不同操作
     * @param int    $userId
     * @param array|null  $data  可选，如果提供则创建或更新资料
     * @return bool|array|null
     */
    public function execute(int $userId, array $data = null)
    {
        // 如果没有提供数据，执行查看操作
        if ($data === null) {
            return $this->viewProfile($userId);
        }
        
        // 检查个人档案是否已存在
        $profile = $this->viewProfile($userId);
        if ($profile === null) {
            // 如果不存在，执行创建操作
            return $this->createProfile($userId, $data);
        } else {
            // 如果存在，执行更新操作
            return $this->updateProfile($userId, $data);
        }
    }

    /**
     * 执行搜索档案操作
     * @param array $criteria ['full_name','role']
     * @return array
     */
    public function executeSearch(array $criteria): array
    {
        return $this->searchProfiles($criteria);
    }

    /**
     * 创建个人档案
     * @param int    $userId
     * @param array  $data     ['full_name','avatar_url','bio','availability']
     * @return bool
     */
    public function createProfile(int $userId, array $data): bool
    {
        $sql = 'INSERT INTO user_profiles
                (user_id, full_name, avatar_url, bio, availability)
                VALUES (?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            'issss',
            $userId,
            $data['full_name'],
            $data['avatar_url'],
            $data['bio'],
            $data['availability']
        );
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /**
     * 查看个人档案
     * @param int    $userId
     * @return array|null
     */
    public function viewProfile(int $userId): ?array
    {
        $sql = 'SELECT full_name, avatar_url, bio, availability
                FROM user_profiles
                WHERE user_id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $fullName,
            $avatarUrl,
            $bio,
            $availability
        );
        $result = null;
        if (mysqli_stmt_fetch($stmt)) {
            $result = [
                'full_name'    => $fullName,
                'avatar_url'   => $avatarUrl,
                'bio'          => $bio,
                'availability' => $availability
            ];
        }
        mysqli_stmt_close($stmt);
        return $result;
    }

    /**
     * 更新个人档案
     * @param int    $userId
     * @param array  $data      任意包含 full_name/avatar_url/bio/availability
     * @return bool
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $fields = [];
        $types  = '';
        $values = [];
        foreach (['full_name','avatar_url','bio','availability'] as $col) {
            if (isset($data[$col])) {
                $fields[] = "$col = ?";
                $types   .= 's';
                $values[] = $data[$col];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $values[] = $userId;
        $sql = 'UPDATE user_profiles
                SET ' . implode(', ', $fields) . '
                WHERE user_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types . 'i', ...$values);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /**
     * 搜索其他用户档案
     * @param array $criteria ['full_name','role']
     * @return array
     */
    public function searchProfiles(array $criteria): array
    {
        $sql = 'SELECT p.user_id, u.role, p.full_name, p.avatar_url, p.bio, p.availability
                FROM user_profiles p
                JOIN users u ON p.user_id = u.id
                WHERE 1=1';
        $types  = '';
        $values = [];
        if (!empty($criteria['full_name'])) {
            $sql   .= ' AND p.full_name LIKE ?';
            $types .= 's';
            $values[] = '%' . $criteria['full_name'] . '%';
        }
        if (!empty($criteria['role'])) {
            $sql   .= ' AND u.role = ?';
            $types .= 's';
            $values[] = $criteria['role'];
        }
        if (!empty($types)) {
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, $types, ...$values);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $profiles = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $profiles[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $profiles;
        } else {
            $result = mysqli_query($this->conn, $sql);
            $profiles = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $profiles[] = $row;
            }
            mysqli_free_result($result);
            return $profiles;
        }
    }

    /**
     * 检查用户是否已有个人档案
     * @param int $userId
     * @return bool
     */
    public function hasProfile(int $userId): bool
    {
        $sql = 'SELECT 1 FROM user_profiles WHERE user_id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }
}
