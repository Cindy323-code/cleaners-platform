<?php
namespace Boundary;

use Controller\SearchAvailableCleanersController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/SearchAvailableCleanersController.php';

// Initialize search criteria
$criteria = [];
$searchTerm = '';

// Check if search term is provided
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['keyword'])) {
    $searchTerm = trim($_GET['keyword']);
    if (!empty($searchTerm)) {
        $criteria['keyword'] = $searchTerm;
    }
}

// Always execute to get results (empty criteria returns all)
$controller = new SearchAvailableCleanersController();
$results = $controller->execute($criteria);
?>

<h2>Find Cleaners</h2>

<div class="card">
    <div class="card-title">Search Cleaners</div>
    <form method="get" class="search-form">
        <div class="form-group">
            <label for="keyword">Search by keyword:</label>
            <input type="text" id="keyword" name="keyword" 
                   placeholder="Search by name, service, or description" 
                   value="<?= htmlspecialchars($searchTerm) ?>">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-small">Search</button>
            <a href="search_available_cleaners.php" class="btn btn-small">Show All</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title"><?= empty($searchTerm) ? 'All Available Services' : 'Search Results' ?></div>
    
    <?php if (empty($results)): ?>
        <div class="notice">
            <p>No cleaners found. Please try another search term.</p>
        </div>
    <?php else: ?>
        <div class="cleaners-grid">
            <?php foreach ($results as $cleaner): ?>
                <div class="cleaner-card">
                    <div class="cleaner-header">
                        <h3><?= htmlspecialchars(!empty($cleaner['full']) ? $cleaner['full'] : $cleaner['username']) ?></h3>
                    </div>
                    
                    <div class="cleaner-content">
                        <?php if (!empty($cleaner['bio'])): ?>
                            <p class="cleaner-bio"><?= htmlspecialchars($cleaner['bio']) ?></p>
                        <?php else: ?>
                            <p class="cleaner-bio">No bio available</p>
                        <?php endif; ?>
                        
                        <?php if (!empty($cleaner['sname'])): ?>
                            <div class="service-info">
                                <h4>Service: <?= htmlspecialchars($cleaner['sname']) ?></h4>
                                <p class="service-type">Type: <?= htmlspecialchars($cleaner['stype'] ?? 'Not specified') ?></p>
                                <p class="service-price">Price: $<?= htmlspecialchars($cleaner['price'] ?? '0') ?></p>
                                <p class="service-id">Service ID: <?= htmlspecialchars($cleaner['service_id'] ?? '') ?></p>
                                <?php if (!empty($cleaner['description'])): ?>
                                    <div class="service-description">Description: <?= htmlspecialchars($cleaner['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cleaner-actions">
                        <a href="view_cleaner_profile.php?id=<?= $cleaner['id'] ?>" class="btn btn-small">View Profile</a>
                        <?php if (!empty($cleaner['sname'])): ?>
                            <form action="/Cleanplatform/boundary/shortlist/add_to_shortlist.php" method="post">
                                <input type="hidden" name="service_id" value="<?= $cleaner['service_id'] ?? '' ?>">
                                <button type="submit" class="btn btn-small btn-secondary">Add to Shortlist</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* 仅保留页面特有样式，移除与全局样式重复的部分 */
.form-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}
.notice {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #007bff;
    margin: 15px 0;
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
.cleaner-fullname {
    color: #6c757d;
    margin: 0;
    font-style: italic;
}
.cleaner-content {
    padding: 15px;
}
.cleaner-bio {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
    font-size: 14px;
}
.service-info {
    background: rgba(0, 123, 255, 0.05);
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}
.service-info h4 {
    margin-top: 0;
    margin-bottom: 5px;
    color: var(--primary-color);
}
.service-type, .service-price {
    margin: 5px 0;
    font-size: 14px;
}
.service-price {
    font-weight: bold;
    color: #28a745;
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
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
