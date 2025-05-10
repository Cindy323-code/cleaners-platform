<?php
// boundary/shortlist/view_shortlist.php
namespace Boundary;

use Controller\ViewShortlistController;
use Controller\SearchShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/ViewShortlistController.php';
require_once __DIR__ . '/../../controller/SearchShortlistController.php';

// Check if user is logged in as homeowner
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Handle search
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
if (!empty($keyword)) {
    $list = (new SearchShortlistController())->execute($_SESSION['user']['id'], $keyword);
} else {
    $list = (new ViewShortlistController())->execute($_SESSION['user']['id']);
}

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$success = isset($_GET['success']) ? (bool)$_GET['success'] : false;
?>

<h2>Your Shortlist</h2>

<?php if ($message): ?>
    <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-title">Search Shortlist</div>
    <form method="get" class="search-form">
        <div class="form-group">
            <label for="keyword">Search by keyword:</label>
            <input type="text" id="keyword" name="keyword" 
                   placeholder="Search by service name..." 
                   value="<?= htmlspecialchars($keyword) ?>">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-small">Search</button>
            <a href="view_shortlist.php" class="btn btn-small">Show All</a>
            <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" class="btn btn-small">Find More Cleaners</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title"><?= empty($keyword) ? 'All Shortlisted Services' : 'Search Results' ?></div>
    
    <?php if (empty($list)): ?>
        <div class="notice">
            <p>Your shortlist is empty. Add some cleaning services to get started.</p>
            <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" class="btn">Browse Available Cleaners</a>
        </div>
    <?php else: ?>
        <div class="cleaners-grid">
            <?php foreach ($list as $item): ?>
                <div class="cleaner-card">
                    <div class="cleaner-header">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <span class="service-type"><?= htmlspecialchars($item['type']) ?></span>
                    </div>
                    
                    <div class="cleaner-content">
                        <p class="service-price">Price: $<?= htmlspecialchars($item['price']) ?></p>
                        <p class="service-id">Service ID: <?= htmlspecialchars($item['service_id']) ?></p>
                        <p class="cleaner-info">Cleaner: <?= htmlspecialchars($item['cleaner_name']) ?></p>
                        
                        <?php
                        // 查询service description using service_id
                        $serviceDescOutput = '';
                        if (!empty($item['service_id'])) {
                            $db = \Config\Database::getConnection();
                            $stmt_desc = mysqli_prepare($db, 'SELECT description FROM cleaner_services WHERE id = ?');
                            mysqli_stmt_bind_param($stmt_desc, 'i', $item['service_id']);
                            mysqli_stmt_execute($stmt_desc);
                            mysqli_stmt_bind_result($stmt_desc, $desc_val);
                            if (mysqli_stmt_fetch($stmt_desc)) {
                                $serviceDescOutput = $desc_val;
                            }
                            mysqli_stmt_close($stmt_desc);
                        }
                        if (!empty($serviceDescOutput)): ?>
                            <div class="service-info">
                                <div class="service-description">Description: <?= htmlspecialchars($serviceDescOutput) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cleaner-actions">
                        <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php?id=<?= htmlspecialchars($item['cleaner_id']) ?>" class="btn btn-small">View Details</a>
                        <form action="remove_from_shortlist.php" method="post" onsubmit="return confirm('Are you sure you want to remove this service from your shortlist?');">
                            <input type="hidden" name="shortlist_id" value="<?= $item['shortlist_id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* 仅保留页面特有样式，使用与search_available_cleaners.php一致的样式 */
.notice {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #007bff;
    margin: 15px 0;
}
.notice .btn {
    margin-top: 10px;
}
.cleaners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
.cleaner-card {
    border: 1px solid #eee;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.cleaner-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.cleaner-header {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #eee;
}
.cleaner-header h3 {
    margin: 0 0 5px 0;
    color: var(--primary-color);
}
.service-type {
    display: inline-block;
    background: rgba(66, 133, 244, 0.1);
    color: var(--primary-color);
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
}
.cleaner-username {
    color: #6c757d;
    margin: 0;
    font-style: italic;
    font-size: 0.9em;
}
.cleaner-content {
    padding: 15px;
}
.service-price {
    font-weight: bold;
    color: #28a745;
    margin: 5px 0;
    font-size: 16px;
}
.service-id {
    margin: 5px 0;
    font-size: 14px;
}
.cleaner-info {
    margin: 5px 0;
    font-size: 14px;
    font-weight: 500;
    color: #555;
}
.service-info {
    background: rgba(0, 123, 255, 0.05);
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}
.cleaner-actions {
    padding: 15px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    gap: 10px;
}
.cleaner-actions form {
    display: inline;
}
/* 修复按钮高度不一致问题 */
.cleaner-actions .btn,
.cleaner-actions button.btn {
    height: 38px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}
.form-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
