<?php
namespace Entity;

use mysqli;
use Config\Database;
require_once __DIR__ . '/User.php'; 
class UserProfile
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * 统一执行方法，根据参数数量和类型分发到不同操作
     * @param int    $userId
     * @param string $userType cleaner|homeowner
     * @param array|null  $data  可选，如果提供则创建或更新资料
     * @return bool|array|null
     */
    public function execute(int $userId, string $userType, array $data = null)
    {
        // 如果没有提供数据，执行查看操作
        if ($data === null) {
            return $this->viewProfile($userId, $userType);
        }
        
        // 检查个人档案是否已存在
        $profile = $this->viewProfile($userId, $userType);
        if ($profile === null) {
            // 如果不存在，执行创建操作
            return $this->createProfile($userId, $userType, $data);
        } else {
            // 如果存在，执行更新操作
            return $this->updateProfile($userId, $userType, $data);
        }
    }

    /**
     * 执行停用档案操作
     * @param int    $userId
     * @param string $userType cleaner|homeowner
     * @return bool
     */
    public function executeDeactivate(int $userId, string $userType): bool
    {
        return $this->deactivateProfile($userId, $userType);
    }

    /**
     * 执行搜索档案操作
     * @param array $criteria ['full_name','user_type']
     * @return array
     */
    public function executeSearch(array $criteria): array
    {
        return $this->searchProfiles($criteria);
    }

    /**
     * 创建个人档案
     * @param int    $userId
     * @param string $userType cleaner|homeowner
     * @param array  $data     ['full_name','avatar_url','bio','availability']
     * @return bool
     */
    public function createProfile(int $userId, string $userType, array $data): bool
    {
        $sql = 'INSERT INTO user_profiles
                (user_id, user_type, full_name, avatar_url, bio, availability, status)
                VALUES (?, ?, ?, ?, ?, ?, "active")';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            'isssss',
            $userId,
            $userType,
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
     * @param string $userType
     * @return array|null
     */
    public function viewProfile(int $userId, string $userType): ?array
    {
        $sql = 'SELECT full_name, avatar_url, bio, availability, status
                FROM user_profiles
                WHERE user_id = ? AND user_type = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $userId, $userType);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $fullName,
            $avatarUrl,
            $bio,
            $availability,
            $status
        );
        $result = null;
        if (mysqli_stmt_fetch($stmt)) {
            $result = [
                'full_name'    => $fullName,
                'avatar_url'   => $avatarUrl,
                'bio'          => $bio,
                'availability' => $availability,
                'status'       => $status
            ];
        }
        mysqli_stmt_close($stmt);
        return $result;
    }

    /**
     * 更新个人档案
     * @param int    $userId
     * @param string $userType
     * @param array  $data      任意包含 full_name/avatar_url/bio/availability
     * @return bool
     */
    public function updateProfile(int $userId, string $userType, array $data): bool
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
        $values[] = $userType;
        $sql = 'UPDATE user_profiles
                SET ' . implode(', ', $fields) . '
                WHERE user_id = ? AND user_type = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types . 'is', ...$values);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /**
     * 暂停/停用个人档案
     * @param int    $userId
     * @param string $userType
     * @return bool
     */
    public function deactivateProfile(int $userId, string $userType): bool
    {
        $sql = 'UPDATE user_profiles
                SET status = "inactive"
                WHERE user_id = ? AND user_type = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $userId, $userType);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /**
     * 搜索其他用户档案
     * @param array $criteria ['full_name','user_type']
     * @return array
     */
    public function searchProfiles(array $criteria): array
    {
        $sql = 'SELECT user_id, user_type, full_name, avatar_url, bio, availability
                FROM user_profiles WHERE 1=1';
        $types  = '';
        $values = [];
        if (!empty($criteria['full_name'])) {
            $sql   .= ' AND full_name LIKE ?';
            $types .= 's';
            $values[] = '%' . $criteria['full_name'] . '%';
        }
        if (!empty($criteria['user_type'])) {
            $sql   .= ' AND user_type = ?';
            $types .= 's';
            $values[] = $criteria['user_type'];
        }
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($values) {
            mysqli_stmt_bind_param($stmt, $types, ...$values);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $userId,
            $userType,
            $fullName,
            $avatarUrl,
            $bio,
            $availability
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('userId','userType','fullName','avatarUrl','bio','availability');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }
}
