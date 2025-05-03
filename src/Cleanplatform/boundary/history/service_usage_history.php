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

<h2>Service Usage History</h2>

<?php if ($detailView && $details): ?>
    <!-- Detail View -->
    <div class="card">
        <div class="card-title">Service Usage Details</div>
        <div class="details-list">
            <div class="detail-item">
                <div class="detail-label">Service ID:</div>
                <div class="detail-value"><?= htmlspecialchars($details['id']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Service Name:</div>
                <div class="detail-value"><?= htmlspecialchars($details['name']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Service Type:</div>
                <div class="detail-value"><?= htmlspecialchars($details['type']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Service Date:</div>
                <div class="detail-value"><?= htmlspecialchars($details['date']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Price Charged:</div>
                <div class="detail-value">$<?= htmlspecialchars($details['price']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status:</div>
                <div class="detail-value">
                    <span class="status-badge status-<?= strtolower($details['status']) ?>">
                        <?= htmlspecialchars($details['status']) ?>
                    </span>
                </div>
            </div>
            <?php if (!empty($details['feedback'])): ?>
            <div class="detail-item">
                <div class="detail-label">Feedback:</div>
                <div class="detail-value"><?= htmlspecialchars($details['feedback']) ?></div>
            </div>
            <?php endif; ?>
        </div>
        <div class="button-group">
            <a href="service_usage_history.php" class="btn">Back to History</a>
            <a href="/Cleanplatform/public/dashboard.php" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>
<?php else: ?>
    <!-- Search View -->
    <div class="card">
        <div class="card-title">Search History</div>
        <form method="get" action="service_usage_history.php" class="search-form">
            <div class="form-row">
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
                        // Get all service types
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
            </div>
            <div class="button-group">
                <button type="submit" class="btn">Search</button>
                <a href="service_usage_history.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-title">History Results</div>
        <?php if (empty($results)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>No Results Found</h3>
                <p>No service usage history found. Try adjusting your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
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
                                    <a href="service_usage_history.php?view_id=<?= $r['id'] ?>" class="btn btn-small">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
.search-form {
    margin-bottom: 20px;
}
.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}
.form-group {
    flex: 1;
    min-width: 200px;
}
.details-list {
    margin: 20px 0;
}
.detail-item {
    display: flex;
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}
.detail-label {
    font-weight: bold;
    width: 150px;
    color: #555;
}
.detail-value {
    flex: 1;
}
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
.table-responsive {
    overflow-x: auto;
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
.status-confirmed {
    background: #fff3cd;
    color: #856404;
}
.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>