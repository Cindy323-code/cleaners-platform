<?php
// boundary/history/service_usage_history.php
namespace Boundary;

use Controller\SearchServiceUsageHistoryController;
use Controller\ViewServiceUsageDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/SearchServiceUsageHistoryController.php';
require_once __DIR__ . '/../../controller/ViewServiceUsageDetailsController.php';

// 验证用户已登录且是房主
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$homeownerId = $_SESSION['user']['id'];
$results = [];
$detailView = false;
$details = null;

// 处理详情查看请求
if (isset($_GET['view_id']) && intval($_GET['view_id']) > 0) {
    $matchId = intval($_GET['view_id']);
    $details = (new ViewServiceUsageDetailsController())->execute($matchId);
    $detailView = true;
}

// 处理搜索请求
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['from']) || isset($_GET['to']) || isset($_GET['type']))) {
    $filter = [];
    
    if (!empty($_GET['from'])) {
        $filter['date_from'] = $_GET['from'];
    }
    
    if (!empty($_GET['to'])) {
        $filter['date_to'] = $_GET['to'];
    }
    
    if (!empty($_GET['type'])) {
        $filter['service_type'] = $_GET['type'];
    }
    
    $results = (new SearchServiceUsageHistoryController())->execute($homeownerId, $filter);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Usage History</title>
    <style>
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        .card-header h3 {
            margin: 0;
            color: #333;
        }
        .card-body {
            padding: 20px;
        }
        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 20px;
        }
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background: #4285f4;
            color: white;
            border: none;
            padding: 9px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-secondary {
            background: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table th {
            background: #f8f9fa;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #4285f4;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .detail-row {
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 12px 0;
        }
        .detail-label {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            width: 70%;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Service Usage History</h2>
        
        <?php if ($detailView && $details): ?>
            <!-- 详情视图 -->
            <a href="service_usage_history.php" class="back-link">&laquo; Back to History</a>
            
            <div class="card">
                <div class="card-header">
                    <h3>Service Usage Details</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-label">Service ID:</div>
                        <div class="detail-value"><?= htmlspecialchars($details['id']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Service Name:</div>
                        <div class="detail-value"><?= htmlspecialchars($details['name']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Service Type:</div>
                        <div class="detail-value"><?= htmlspecialchars($details['type']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Service Date:</div>
                        <div class="detail-value"><?= htmlspecialchars($details['date']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Price Charged:</div>
                        <div class="detail-value">$<?= htmlspecialchars($details['price']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <span class="status-badge status-<?= strtolower($details['status']) ?>">
                                <?= htmlspecialchars($details['status']) ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($details['feedback'])): ?>
                    <div class="detail-row">
                        <div class="detail-label">Feedback:</div>
                        <div class="detail-value"><?= htmlspecialchars($details['feedback']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- 搜索视图 -->
            <div class="card">
                <div class="card-header">
                    <h3>Search History</h3>
                </div>
                <div class="card-body">
                    <form method="get" action="service_usage_history.php" class="search-form">
                        <div class="form-group">
                            <label for="from">From Date:</label>
                            <input type="date" id="from" name="from" value="<?= isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="to">To Date:</label>
                            <input type="date" id="to" name="to" value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="type">Service Type:</label>
                            <select id="type" name="type">
                                <option value="">All Types</option>
                                <?php
                                // 获取所有服务类型
                                $db = \Config\Database::getConnection();
                                $typesSql = "SELECT DISTINCT type FROM cleaner_services ORDER BY type";
                                $typesResult = mysqli_query($db, $typesSql);
                                while ($type = mysqli_fetch_assoc($typesResult)) {
                                    $selected = (isset($_GET['type']) && $_GET['type'] === $type['type']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($type['type']) . '" ' . $selected . '>' . htmlspecialchars($type['type']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Search</button>
                        <a href="service_usage_history.php" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
            </div>
            
            <!-- 结果表格 -->
            <div class="card">
                <div class="card-header">
                    <h3>History Results</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($results)): ?>
                        <div class="empty-state">
                            <p>No service usage history found. Try adjusting your search criteria.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service Name</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id']) ?></td>
                                        <td><?= htmlspecialchars($r['name']) ?></td>
                                        <td><?= htmlspecialchars($r['type']) ?></td>
                                        <td><?= htmlspecialchars($r['date']) ?></td>
                                        <td>$<?= htmlspecialchars($r['price']) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower($r['status']) ?>">
                                                <?= htmlspecialchars($r['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="service_usage_history.php?view_id=<?= $r['id'] ?>" class="btn">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>