<?php
// entity/HomeOwnerUser.php
namespace Entity;
require_once __DIR__ . '/User.php'; 

class HomeOwnerUser extends User {
    protected static string $tableName = 'homeowners';

    /** 搜索可用清洁工 */
    public function searchAvailableCleaners(array $criteria): array {
        // 示例查询：按服务类型和最低评分
        $sql = 'SELECT c.id,c.username,s.name,s.type,s.price'
             . ' FROM cleaners c'
             . ' JOIN cleaner_services s ON c.id = s.cleaner_id'
             . ' WHERE s.type = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $criteria['service_type']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,$id,$username,$sname,$stype,$price
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = [
                'cleaner_id'=>$id,
                'username'=>$username,
                'service_name'=>$sname,
                'service_type'=>$stype,
                'price'=>$price
            ];
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /** 查看清洁工详情 */
    public function viewCleanerProfile(int $cleanerId): ?array {
        $sql = 'SELECT id,username,profile,bio FROM homeowners WHERE id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$id,$username,$profile,$bio);
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return compact('id','username','profile','bio');
        }
        mysqli_stmt_close($stmt);
        return null;
    }

    /** 添加至收藏 */
    public function addToShortlist(int $homeownerId, int $serviceId): bool {
        $sql = 'INSERT INTO shortlists (homeowner_id,service_id,added_at) VALUES (?,?,NOW())';
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
}