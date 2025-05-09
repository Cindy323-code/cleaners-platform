<?php
// entity/User.php
namespace Entity;

use Config\Database;

abstract class User
{
    protected $conn;
    protected static string $tableName = 'users';

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Abstract method to be implemented by subclasses
    public abstract function createUser(array $data): bool;

    /**
     * 执行登录操作，对应各Login控制器
     * @param string $username
     * @param string $password
     * @return array|null
     */
    public function executeLogin(string $username, string $password): ?array
    {
        return $this->login($username, $password);
    }

    /**
     * 执行登出操作，对应各Logout控制器
     */
    public function executeLogout(): void
    {
        $this->logout();
    }

    // Static factory method to get the correct User entity instance
    public static function getInstance(array $data): User {
        switch ($data['role']) {
            case 'admin':
            case 'manager': // Assuming 'manager' uses AdminUser
                return new AdminUser();
            case 'cleaner':
                return new CleanerUser();
            case 'homeowner':
                return new HomeOwnerUser();
            case 'platform_manager': // Added PlatformManager
                return new PlatformManager();
            default:
                // Or handle error, throw exception, or return a default User type if applicable
                throw new \InvalidArgumentException("Unsupported role: " . $data['role']);
        }
    }

    /** 通用登录 */
    public function login(string $username, string $password): ?array
    {
        $sql = 'SELECT id, username, password_hash, role, status
                FROM ' . static::$tableName . ' WHERE username = ? LIMIT 1';
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
                    'role'     => strtolower($role),
                    'status'   => $status
                ];
            }
        }
        mysqli_stmt_close($stmt);
        return $result;          // 成功返回数组，失败 null
    }

    /** 通用登出 */
    public function logout(): void
    {
        session_destroy();
    }
}
