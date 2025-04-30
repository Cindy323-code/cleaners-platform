<?php
// entity/PlatformManager.php
namespace Entity;
require_once __DIR__ . '/User.php'; 

class PlatformManager extends User {
    protected static string $tableName = 'platform_managers';

    /** 创建服务类别 */
    public function createCategory(string $name, string $description): bool {
        $sql = 'INSERT INTO service_categories (name,description,created_at) VALUES (?,?,NOW())';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $name, $description);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 查看所有类别 */
    public function viewCategories(): array {
        $sql = 'SELECT id,name,description,created_at FROM service_categories';
        $res = mysqli_query($this->conn, $sql);
        $cats = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $cats[] = $row;
        }
        return $cats;
    }

    /** 更新类别 */
    public function updateCategory(int $id, array $fields): bool {
        $sets = [];
        $types = '';
        $vals  = [];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = ?";
            $types .= 's';
            $vals[]  = $val;
        }
        $sql = 'UPDATE service_categories SET ' . implode(',', $sets) . ' WHERE id = ?';
        $types .= 'i';
        $vals[] = $id;
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$vals);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 删除类别 */
    public function deleteCategory(int $id): bool {
        $sql = 'DELETE FROM service_categories WHERE id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** 搜索类别 */
    public function searchCategory(string $keyword): array {
        $like = "%$keyword%";
        $stmt = mysqli_prepare(
            $this->conn,
            'SELECT id,name,description FROM service_categories WHERE name LIKE ?'
        );
        mysqli_stmt_bind_param($stmt, 's', $like);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $name, $description);
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','description');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 生成日报 */
    public function generateDailyReport(string $date): array {
        // 示例：统计当天注册、服务数量等
        $sql = "SELECT COUNT(*) AS new_users FROM admin_users WHERE DATE(created_at)=?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $date);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $newUsers);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return ['new_users' => $newUsers];
    }

    /** 生成周报 */
    public function generateWeeklyReport(string $startDate, string $endDate): array {
        $sql = "SELECT COUNT(*) AS new_users FROM admin_users"
             . " WHERE DATE(created_at) BETWEEN ? AND ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $newUsers);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return ['new_users' => $newUsers];
    }

    /** 生成月报 */
    public function generateMonthlyReport(int $year, int $month): array {
        $sql = "SELECT COUNT(*) AS new_users FROM admin_users"
             . " WHERE YEAR(created_at)=? AND MONTH(created_at)=?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $year, $month);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $newUsers);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return ['new_users' => $newUsers];
    }
}
