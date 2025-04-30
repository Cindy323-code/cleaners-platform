<?php
// entity/User.php
namespace Entity;

abstract class User
{
    protected $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /** 通用登录 */
    public function login(string $username, string $password): ?array
    {
        $sql = 'SELECT id, username, password_hash, role, status
                FROM admin_users WHERE username = ? LIMIT 1';
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
        return $result;          // 成功返回数组，失败 null
    }

    /** 通用登出 */
    public function logout(): void
    {
        session_destroy();
    }
}
