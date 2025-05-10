<?php
namespace Entity;

use mysqli;
use Config\Database;
require_once __DIR__ . '/User.php'; 
class MatchHistory
{
    private mysqli $conn;

    // Static factory method to get MatchHistory instance
    public static function getInstance(): MatchHistory {
        return new self();
    }

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }
    
    /**
     * 执行查看匹配详情操作，对应ViewConfirmedMatchDetailsController
     * @param int $matchId
     * @return array|null
     */
    public function executeReadMatchDetails(int $matchId): ?array
    {
        return $this->getConfirmedMatchDetails($matchId);
    }
    
    /**
     * 执行搜索确认匹配操作，对应SearchConfirmedMatchesController
     * @param int $cleanerId
     * @param array $f
     * @return array
     */
    public function executeSearchConfirmedMatches(int $cleanerId, array $f): array
    {
        return $this->searchConfirmedMatches($cleanerId, $f);
    }
    
    /**
     * 执行搜索服务使用历史操作，对应SearchServiceUsageHistoryController
     * @param int $homeownerId
     * @param array $f
     * @return array
     */
    public function executeSearchUsageHistory(int $homeownerId, array $f): array
    {
        return $this->searchUsageHistory($homeownerId, $f);
    }
    
    /**
     * 执行查看服务使用详情操作，对应ViewServiceUsageDetailsController
     * @param int $historyId
     * @return array|null
     */
    public function executeGetUsageDetails(int $historyId): ?array
    {
        return $this->getUsageDetails($historyId);
    }
    
    /**
     * 执行查看服务浏览量操作，对应ViewServiceProfileViewsController
     * @param int $cleanerId
     * @return int
     */
    public function executeGetViewCount(int $cleanerId): int
    {
        return $this->getViewCount($cleanerId);
    }
    
    /**
     * 执行查看服务收藏数操作，对应ViewServiceShortlistCountController
     * @param int $cleanerId
     * @return int
     */
    public function executeGetShortlistCount(int $cleanerId): int
    {
        return $this->getShortlistCount($cleanerId);
    }

    /**
     * 合计某位 Cleaner 的服务档案浏览量
     */
    public function getViewCount(int $cleanerId): int
    {
        $sql = 'SELECT SUM(s.view_count)
                FROM service_stats s
                JOIN cleaner_services cs ON cs.id = s.service_id
                WHERE cs.user_id = ?';
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
                WHERE cs.user_id = ?';
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
        $sql = 'SELECT mh.id, cs.name as service_name, cs.type, mh.service_date, mh.price_charged, mh.status,
                       u.username as homeowner_name
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                JOIN users u ON u.id = mh.homeowner_id
                WHERE mh.cleaner_id = ?';
        $types  = 'i';
        $values = [$cleanerId];

        if (!empty($f['service_type'])) {
            $sql   .= ' AND cs.type = ?';
            $types .= 's';
            $values[] = $f['service_type'];
        }
        if (!empty($f['from'])) {
            $sql   .= ' AND mh.service_date >= ?';
            $types .= 's';
            $values[] = $f['from'];
        }
        if (!empty($f['to'])) {
            $sql   .= ' AND mh.service_date <= ?';
            $types .= 's';
            $values[] = $f['to'];
        }

        $sql .= ' ORDER BY mh.service_date DESC';

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $service_name,
            $type,
            $service_date,
            $price,
            $status,
            $homeowner_name
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id','service_name','type','service_date','price','status','homeowner_name');
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

        $sql .= ' ORDER BY mh.service_date DESC';

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
