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

// 处理搜索请求或加载所有历史记录
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filter = [];

    // Basic filters
    if (!empty($_GET['from'])) {
        $filter['date_from'] = $_GET['from'];
    }

    if (!empty($_GET['to'])) {
        $filter['date_to'] = $_GET['to'];
    }

    if (!empty($_GET['type'])) {
        $filter['service_type'] = $_GET['type'];
    }
    
    // Enhanced filters
    if (!empty($_GET['status'])) {
        $filter['status'] = $_GET['status'];
    }
    
    if (!empty($_GET['price_min'])) {
        $filter['price_min'] = $_GET['price_min'];
    }
    
    if (!empty($_GET['price_max'])) {
        $filter['price_max'] = $_GET['price_max'];
    }
    
    if (!empty($_GET['cleaner'])) {
        $filter['cleaner'] = $_GET['cleaner'];
    }
    
    if (!empty($_GET['service_name'])) {
        $filter['service_name'] = $_GET['service_name'];
    }
    
    // Sorting options
    if (!empty($_GET['sort_by'])) {
        $filter['sort_by'] = $_GET['sort_by'];
    }
    
    if (!empty($_GET['sort_dir'])) {
        $filter['sort_dir'] = $_GET['sort_dir'];
    }

    $results = (new SearchServiceUsageHistoryController())->execute($homeownerId, $filter);
}
?>

<h2>Service Usage History</h2>

<?php if ($detailView && $details): ?>
    <!-- Detail View -->
    <div class="card">
        <div class="card-title">Service Usage Details</div>
        
        <!-- Cleaner Info -->
        <div class="cleaner-profile">
            <div class="cleaner-avatar">
                <?php if (!empty($details['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($details['avatar_url']) ?>" alt="Cleaner Avatar">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?= htmlspecialchars(substr($details['cleaner_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="cleaner-info">
                <h3><?= htmlspecialchars($details['cleaner_name']) ?></h3>
                <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php?id=<?= $details['cleaner_id'] ?>" 
                   class="btn btn-small">View Profile</a>
            </div>
        </div>
        
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
            <?php if (!empty($details['description'])): ?>
            <div class="detail-item">
                <div class="detail-label">Description:</div>
                <div class="detail-value"><?= htmlspecialchars($details['description']) ?></div>
            </div>
            <?php endif; ?>
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
                        // Get only service types from homeowner's history
                        $db = \Config\Database::getConnection();
                        $typesSql = "SELECT DISTINCT cs.type 
                                     FROM match_histories mh 
                                     JOIN cleaner_services cs ON cs.id = mh.service_id 
                                     WHERE mh.homeowner_id = ? 
                                     ORDER BY cs.type";
                        $stmt = mysqli_prepare($db, $typesSql);
                        mysqli_stmt_bind_param($stmt, 'i', $homeownerId);
                        mysqli_stmt_execute($stmt);
                        $typesResult = mysqli_stmt_get_result($stmt);
                        
                        while ($type = mysqli_fetch_assoc($typesResult)) {
                            $selected = (isset($_GET['type']) && $_GET['type'] === $type['type']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($type['type']) . '" ' . $selected . '>' . htmlspecialchars($type['type']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <!-- Enhanced filters -->
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price_min">Min Price:</label>
                    <input type="number" id="price_min" name="price_min" min="0" step="0.01" 
                           value="<?= isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="price_max">Max Price:</label>
                    <input type="number" id="price_max" name="price_max" min="0" step="0.01"
                           value="<?= isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '' ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cleaner">Cleaner:</label>
                    <input type="text" id="cleaner" name="cleaner" 
                           value="<?= isset($_GET['cleaner']) ? htmlspecialchars($_GET['cleaner']) : '' ?>" 
                           placeholder="Enter cleaner name...">
                </div>
                <div class="form-group">
                    <label for="service_name">Service Name:</label>
                    <input type="text" id="service_name" name="service_name" 
                           value="<?= isset($_GET['service_name']) ? htmlspecialchars($_GET['service_name']) : '' ?>" 
                           placeholder="Enter service name...">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sort_by">Sort By:</label>
                    <select id="sort_by" name="sort_by">
                        <option value="">Default (Date)</option>
                        <option value="date" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'date') ? 'selected' : '' ?>>Date</option>
                        <option value="price" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'price') ? 'selected' : '' ?>>Price</option>
                        <option value="type" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'type') ? 'selected' : '' ?>>Type</option>
                        <option value="status" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'status') ? 'selected' : '' ?>>Status</option>
                        <option value="cleaner" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'cleaner') ? 'selected' : '' ?>>Cleaner</option>
                        <option value="service" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'service') ? 'selected' : '' ?>>Service Name</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sort_dir">Sort Direction:</label>
                    <select id="sort_dir" name="sort_dir">
                        <option value="asc" <?= (isset($_GET['sort_dir']) && $_GET['sort_dir'] === 'asc') ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= (isset($_GET['sort_dir']) && $_GET['sort_dir'] === 'desc') ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-small">Search</button>
                <a href="service_usage_history.php" class="btn btn-small btn-secondary">Reset</a>
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
                            <th>Cleaner</th>
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
                                <td><?= htmlspecialchars($r['cleaner_name']) ?></td>
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
.cleaner-profile {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
}
.cleaner-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 20px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.cleaner-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.avatar-placeholder {
    font-size: 28px;
    color: var(--primary-color);
    text-transform: uppercase;
}
.cleaner-info {
    flex: 1;
}
.cleaner-info h3 {
    margin: 0 0 10px 0;
    color: var(--primary-color);
    font-size: 18px;
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
.empty-state {
    text-align: center;
    padding: 30px 20px;
    background: #f8f9fa;
    border-radius: 5px;
    margin: 15px 0;
}
.empty-icon {
    font-size: 36px;
    margin-bottom: 10px;
}
.empty-state h3 {
    margin-bottom: 10px;
    color: #555;
    font-size: 18px;
}
.empty-state p {
    margin-bottom: 15px;
    color: #777;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>