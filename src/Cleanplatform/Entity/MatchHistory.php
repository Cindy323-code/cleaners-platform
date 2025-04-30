<?php
namespace Entity;

use mysqli;
require_once __DIR__ . '/User.php'; 
class MatchHistory
{
    private mysqli $conn;

    public function __construct(mysqli $db)
    {
        $this->conn = $db;
    }

    /**
     * 合计某位 Cleaner 的服务档案浏览量
     */
    public function getViewCount(int $cleanerId): int
    {
        $sql = 'SELECT SUM(s.view_count)
                FROM service_stats s
                JOIN cleaner_services cs ON cs.id = s.service_id
                WHERE cs.cleaner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return (int)$total;
    }

    /**
     * 合计某位 Cleaner 的服务被收藏次数
     */
    public function getShortlistCount(int $cleanerId): int
    {
        $sql = 'SELECT SUM(s.shortlist_count)
                FROM service_stats s
                JOIN cleaner_services cs ON cs.id = s.service_id
                WHERE cs.cleaner_id = ?';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return (int)$total;
    }

    /**
     * 按服务类型 & 日期区间搜索 Cleaner 的确认匹配历史
     * @param int   $cleanerId
     * @param array $f ['service_type','date_from','date_to']
     * @return array
     */
    public function searchConfirmedMatches(int $cleanerId, array $f): array
    {
        $sql = 'SELECT mh.id, cs.name, cs.type, mh.service_date, mh.price_charged, mh.status
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                WHERE mh.cleaner_id = ?';
        $types  = 'i';
        $values = [$cleanerId];

        if (!empty($f['service_type'])) {
            $sql   .= ' AND cs.type = ?';
            $types .= 's';
            $values[] = $f['service_type'];
        }
        if (!empty($f['date_from'])) {
            $sql   .= ' AND mh.service_date >= ?';
            $types .= 's';
            $values[] = $f['date_from'];
        }
        if (!empty($f['date_to'])) {
            $sql   .= ' AND mh.service_date <= ?';
            $types .= 's';
            $values[] = $f['date_to'];
        }

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $name,
            $type,
            $date,
            $price,
            $status
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','type','date','price','status');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /**
     * 查看单个匹配的详细信息
     */
    public function getConfirmedMatchDetails(int $matchId): ?array
    {
        $sql = 'SELECT mh.id, cs.name, cs.type, mh.service_date,
                       mh.price_charged, mh.status, mh.feedback
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                WHERE mh.id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $matchId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $name,
            $type,
            $date,
            $price,
            $status,
            $feedback
        );
        $result = null;
        if (mysqli_stmt_fetch($stmt)) {
            $result = compact('id','name','type','date','price','status','feedback');
        }
        mysqli_stmt_close($stmt);
        return $result;
    }

    /**
     * 按服务类型 & 日期区间搜索 Homeowner 的使用历史
     */
    public function searchUsageHistory(int $homeownerId, array $f): array
    {
        $sql = 'SELECT mh.id, cs.name, cs.type, mh.service_date, mh.price_charged, mh.status
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                WHERE mh.homeowner_id = ?';
        $types  = 'i';
        $values = [$homeownerId];

        if (!empty($f['service_type'])) {
            $sql   .= ' AND cs.type = ?';
            $types .= 's';
            $values[] = $f['service_type'];
        }
        if (!empty($f['date_from'])) {
            $sql   .= ' AND mh.service_date >= ?';
            $types .= 's';
            $values[] = $f['date_from'];
        }
        if (!empty($f['date_to'])) {
            $sql   .= ' AND mh.service_date <= ?';
            $types .= 's';
            $values[] = $f['date_to'];
        }

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $name,
            $type,
            $date,
            $price,
            $status
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','name','type','date','price','status');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /**
     * 查看单个使用记录的详细信息
     */
    public function getUsageDetails(int $historyId): ?array
    {
        $sql = 'SELECT mh.id, cs.name, cs.type, mh.service_date,
                       mh.price_charged, mh.status, mh.feedback
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                WHERE mh.id = ? AND mh.homeowner_id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $historyId, $_SESSION['user']['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $name,
            $type,
            $date,
            $price,
            $status,
            $feedback
        );
        $result = null;
        if (mysqli_stmt_fetch($stmt)) {
            $result = compact('id','name','type','date','price','status','feedback');
        }
        mysqli_stmt_close($stmt);
        return $result;
    }
}
