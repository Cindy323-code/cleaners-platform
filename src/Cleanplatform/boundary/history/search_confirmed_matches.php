<?php
// boundary/history/search_confirmed_matches.php
namespace Boundary;

use Controller\SearchConfirmedMatchesController;
use Controller\ViewConfirmedMatchDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/SearchConfirmedMatchesController.php';
require_once __DIR__ . '/../../controller/ViewConfirmedMatchDetailsController.php';

// Verify user is a cleaner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Get current cleaner's ID
$cleanerId = $_SESSION['user']['id'];

// Initialize variables
$results = [];
$detailView = false;
$details = null;

// Process detail view request
if (isset($_GET['view_id']) && intval($_GET['view_id']) > 0) {
    $matchId = intval($_GET['view_id']);
    $detailsController = new ViewConfirmedMatchDetailsController();
    $details = $detailsController->execute($matchId);
    $detailView = true;
}

// Process search request or load all history
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
    
    if (!empty($_GET['homeowner'])) {
        $filter['homeowner'] = $_GET['homeowner'];
    }
    
    // Sorting options
    if (!empty($_GET['sort_by'])) {
        $filter['sort_by'] = $_GET['sort_by'];
    }
    
    if (!empty($_GET['sort_dir'])) {
        $filter['sort_dir'] = $_GET['sort_dir'];
    }

    $controller = new SearchConfirmedMatchesController();
    $results = $controller->execute($cleanerId, $filter);
}
?>

<h2>Service Match History</h2>

<?php if ($detailView && $details): ?>
    <!-- Detail View -->
    <div class="card">
        <div class="card-title">Service Match Details</div>
        
        <!-- Homeowner Info -->
        <div class="homeowner-profile">
            <div class="homeowner-avatar">
                <?php if (!empty($details['homeowner_avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($details['homeowner_avatar_url']) ?>" alt="Homeowner Avatar">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?= htmlspecialchars(substr($details['homeowner_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="homeowner-info">
                <h3><?= htmlspecialchars($details['homeowner_name']) ?></h3>
            </div>
        </div>
        
        <div class="details-list">
            <div class="detail-item">
                <div class="detail-label">Match ID:</div>
                <div class="detail-value"><?= htmlspecialchars($details['id']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Service Name:</div>
                <div class="detail-value"><?= htmlspecialchars($details['service_name']) ?></div>
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
                <div class="detail-value"><?= htmlspecialchars($details['service_date']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Price Charged:</div>
                <div class="detail-value">$<?= htmlspecialchars($details['price_charged']) ?></div>
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
            <a href="search_confirmed_matches.php" class="btn">Back to History</a>
            <a href="/Cleanplatform/public/dashboard.php" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>
<?php else: ?>
    <!-- Search View -->
    <div class="card">
        <div class="card-title">Search Match History</div>
        <form method="get" action="search_confirmed_matches.php" class="search-form">
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
                        $typesSql = "SELECT DISTINCT type FROM cleaner_services WHERE user_id = ? ORDER BY type";
                        $stmt = mysqli_prepare($db, $typesSql);
                        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
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
                    <label for="homeowner">Homeowner:</label>
                    <input type="text" id="homeowner" name="homeowner" 
                           value="<?= isset($_GET['homeowner']) ? htmlspecialchars($_GET['homeowner']) : '' ?>" 
                           placeholder="Enter homeowner name...">
                </div>
                <div class="form-group">
                    <label for="sort_by">Sort By:</label>
                    <select id="sort_by" name="sort_by">
                        <option value="">Default (Date)</option>
                        <option value="date" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'date') ? 'selected' : '' ?>>Date</option>
                        <option value="price" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'price') ? 'selected' : '' ?>>Price</option>
                        <option value="type" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'type') ? 'selected' : '' ?>>Type</option>
                        <option value="status" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'status') ? 'selected' : '' ?>>Status</option>
                        <option value="homeowner" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'homeowner') ? 'selected' : '' ?>>Homeowner</option>
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
                <a href="search_confirmed_matches.php" class="btn btn-small btn-secondary">Reset</a>
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
                <p>No service match history found. Try adjusting your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Type</th>
                            <th>Homeowner</th>
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
                                <td><?= htmlspecialchars($r['service_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($r['type'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($r['homeowner_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($r['service_date']) ?></td>
                                <td>$<?= htmlspecialchars($r['price_charged']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($r['status']) ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="search_confirmed_matches.php?view_id=<?= $r['id'] ?>" class="btn btn-small">View Details</a>
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
.card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    padding: 20px;
}
.card-title {
    border-bottom: 1px solid #eee;
    font-size: 18px;
    margin: -20px -20px 20px;
    padding: 15px 20px;
    background: #f8f9fa;
}
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
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
.homeowner-profile {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
}
.homeowner-avatar {
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
.homeowner-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #eee;
    color: #888;
    font-size: 28px;
    font-weight: bold;
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
.empty-state {
    text-align: center;
    padding: 30px;
}
.empty-icon {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 15px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
table th,
table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}
table th {
    background-color: #f5f5f5;
    font-weight: bold;
}
table tbody tr:hover {
    background-color: #f8f9fa;
}
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}
.status-confirmed {
    background-color: #d4edda;
    color: #155724;
}
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}
.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}
.table-responsive {
    overflow-x: auto;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
