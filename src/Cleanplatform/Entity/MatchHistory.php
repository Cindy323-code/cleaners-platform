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
     * @param array $f ['service_type','date_from','date_to', 'status', 'price_min', 'price_max', 'sort_by', 'sort_dir']
     * @return array
     */
    public function searchConfirmedMatches(int $cleanerId, array $f): array
    {
        $sql = 'SELECT mh.id, cs.name as service_name, cs.type, cs.description, mh.service_date, mh.price_charged, mh.status,
                       u.id as homeowner_id, u.username as homeowner_username, 
                       COALESCE(up.full_name, u.username) as homeowner_name, up.avatar_url as homeowner_avatar_url
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                JOIN users u ON u.id = mh.homeowner_id
                LEFT JOIN user_profiles up ON up.user_id = u.id
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
        
        // Add status filter
        if (!empty($f['status'])) {
            $sql   .= ' AND mh.status = ?';
            $types .= 's';
            $values[] = $f['status'];
        }
        
        // Add price range filter
        if (!empty($f['price_min'])) {
            $sql   .= ' AND mh.price_charged >= ?';
            $types .= 'd';
            $values[] = $f['price_min'];
        }
        if (!empty($f['price_max'])) {
            $sql   .= ' AND mh.price_charged <= ?';
            $types .= 'd';
            $values[] = $f['price_max'];
        }
        
        // Add homeowner name/username filter
        if (!empty($f['homeowner'])) {
            $sql   .= ' AND (u.username LIKE ? OR up.full_name LIKE ?)';
            $types .= 'ss';
            $like = '%' . $f['homeowner'] . '%';
            $values[] = $like;
            $values[] = $like;
        }

        // Add sorting
        if (!empty($f['sort_by'])) {
            $sortDirection = (!empty($f['sort_dir']) && strtolower($f['sort_dir']) === 'desc') ? 'DESC' : 'ASC';
            $sortColumn = '';
            
            switch($f['sort_by']) {
                case 'date':
                    $sortColumn = 'mh.service_date';
                    break;
                case 'price':
                    $sortColumn = 'mh.price_charged';
                    break;
                case 'type':
                    $sortColumn = 'cs.type';
                    break;
                case 'status':
                    $sortColumn = 'mh.status';
                    break;
                case 'homeowner':
                    $sortColumn = 'homeowner_name';
                    break;
                default:
                    $sortColumn = 'mh.service_date';
            }
            
            $sql .= ' ORDER BY ' . $sortColumn . ' ' . $sortDirection;
        } else {
            $sql .= ' ORDER BY mh.service_date DESC';
        }

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $service_name,
            $type,
            $description,
            $service_date,
            $price_charged,
            $status,
            $homeowner_id,
            $homeowner_username,
            $homeowner_name,
            $homeowner_avatar_url
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id', 'service_name', 'type', 'description', 'service_date', 'price_charged', 'status',
                              'homeowner_id', 'homeowner_username', 'homeowner_name', 'homeowner_avatar_url');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /**
     * 查看单个匹配的详细信息
     */
    public function getConfirmedMatchDetails(int $matchId): ?array
    {
        $sql = 'SELECT mh.id, cs.name as service_name, cs.type, cs.description, mh.service_date,
                       mh.price_charged, mh.status, mh.feedback,
                       u.id as homeowner_id, u.username as homeowner_username,
                       COALESCE(up.full_name, u.username) as homeowner_name, up.avatar_url as homeowner_avatar_url
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                JOIN users u ON u.id = mh.homeowner_id
                LEFT JOIN user_profiles up ON up.user_id = u.id
                WHERE mh.id = ? AND mh.cleaner_id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $matchId, $_SESSION['user']['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $service_name,
            $type,
            $description,
            $service_date,
            $price_charged,
            $status,
            $feedback,
            $homeowner_id,
            $homeowner_username,
            $homeowner_name,
            $homeowner_avatar_url
        );
        $result = null;
        if (mysqli_stmt_fetch($stmt)) {
            $result = compact('id', 'service_name', 'type', 'description', 'service_date', 'price_charged', 'status', 'feedback',
                              'homeowner_id', 'homeowner_username', 'homeowner_name', 'homeowner_avatar_url');
        }
        mysqli_stmt_close($stmt);
        return $result;
    }

    /**
     * 按服务类型 & 日期区间搜索 Homeowner 的使用历史
     * @param int $homeownerId
     * @param array $f ['service_type','date_from','date_to', 'status', 'price_min', 'price_max', 'cleaner', 'sort_by', 'sort_dir']
     * @return array
     */
    public function searchUsageHistory(int $homeownerId, array $f): array
    {
        $sql = 'SELECT mh.id, cs.name, cs.type, cs.description, mh.service_date, mh.price_charged, mh.status,
                       u.id as cleaner_id, u.username as cleaner_username, 
                       COALESCE(up.full_name, u.username) as cleaner_name, up.avatar_url
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                JOIN users u ON u.id = mh.cleaner_id
                LEFT JOIN user_profiles up ON up.user_id = u.id
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
        
        // Add status filter
        if (!empty($f['status'])) {
            $sql   .= ' AND mh.status = ?';
            $types .= 's';
            $values[] = $f['status'];
        }
        
        // Add price range filter
        if (!empty($f['price_min'])) {
            $sql   .= ' AND mh.price_charged >= ?';
            $types .= 'd';
            $values[] = $f['price_min'];
        }
        if (!empty($f['price_max'])) {
            $sql   .= ' AND mh.price_charged <= ?';
            $types .= 'd';
            $values[] = $f['price_max'];
        }
        
        // Add cleaner name filter
        if (!empty($f['cleaner'])) {
            $sql   .= ' AND (u.username LIKE ? OR up.full_name LIKE ?)';
            $types .= 'ss';
            $like = '%' . $f['cleaner'] . '%';
            $values[] = $like;
            $values[] = $like;
        }
        
        // Add service name filter
        if (!empty($f['service_name'])) {
            $sql   .= ' AND cs.name LIKE ?';
            $types .= 's';
            $values[] = '%' . $f['service_name'] . '%';
        }

        // Add sorting
        if (!empty($f['sort_by'])) {
            $sortDirection = (!empty($f['sort_dir']) && strtolower($f['sort_dir']) === 'desc') ? 'DESC' : 'ASC';
            $sortColumn = '';
            
            switch($f['sort_by']) {
                case 'date':
                    $sortColumn = 'mh.service_date';
                    break;
                case 'price':
                    $sortColumn = 'mh.price_charged';
                    break;
                case 'type':
                    $sortColumn = 'cs.type';
                    break;
                case 'status':
                    $sortColumn = 'mh.status';
                    break;
                case 'cleaner':
                    $sortColumn = 'cleaner_name';
                    break;
                case 'service':
                    $sortColumn = 'cs.name';
                    break;
                default:
                    $sortColumn = 'mh.service_date';
            }
            
            $sql .= ' ORDER BY ' . $sortColumn . ' ' . $sortDirection;
        } else {
            $sql .= ' ORDER BY mh.service_date DESC';
        }

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $name,
            $type,
            $description,
            $date,
            $price,
            $status,
            $cleaner_id,
            $cleaner_username,
            $cleaner_name,
            $avatar_url
        );
        $res = [];
        while (mysqli_stmt_fetch($stmt)) {
            $res[] = compact('id', 'name', 'type', 'description', 'date', 'price', 'status', 
                              'cleaner_id', 'cleaner_username', 'cleaner_name', 'avatar_url');
        }
        mysqli_stmt_close($stmt);
        return $res;
    }

    /**
     * 查看单个使用记录的详细信息
     */
    public function getUsageDetails(int $historyId): ?array
    {
        $sql = 'SELECT mh.id, cs.name, cs.type, cs.description, mh.service_date,
                       mh.price_charged, mh.status, mh.feedback,
                       u.id as cleaner_id, u.username as cleaner_username, 
                       COALESCE(up.full_name, u.username) as cleaner_name, up.avatar_url, up.bio as cleaner_bio
                FROM match_histories mh
                JOIN cleaner_services cs ON cs.id = mh.service_id
                JOIN users u ON u.id = mh.cleaner_id
                LEFT JOIN user_profiles up ON up.user_id = u.id
                WHERE mh.id = ? AND mh.homeowner_id = ? LIMIT 1';
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $historyId, $_SESSION['user']['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $name,
            $type,
            $description,
            $date,
            $price,
            $status,
            $feedback,
            $cleaner_id,
            $cleaner_username,
            $cleaner_name,
            $avatar_url,
            $cleaner_bio
        );
        $result = null;
        if (mysqli_stmt_fetch($stmt)) {
            $result = compact('id', 'name', 'type', 'description', 'date', 'price', 'status', 'feedback',
                              'cleaner_id', 'cleaner_username', 'cleaner_name', 'avatar_url', 'cleaner_bio');
        }
        mysqli_stmt_close($stmt);
        return $result;
    }
}
