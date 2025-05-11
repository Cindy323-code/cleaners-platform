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

// 处理搜索请求
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $criteria = [];
    
    // Basic search
    if (!empty($_GET['keyword'])) {
        $criteria['keyword'] = $_GET['keyword'];
    }
    
    if (!empty($_GET['service_type'])) {
        $criteria['service_type'] = $_GET['service_type'];
    }
    
    // Enhanced filters
    if (!empty($_GET['price_min'])) {
        $criteria['price_min'] = $_GET['price_min'];
    }
    
    if (!empty($_GET['price_max'])) {
        $criteria['price_max'] = $_GET['price_max'];
    }
    
    if (!empty($_GET['availability'])) {
        $criteria['availability'] = $_GET['availability'];
    }
    
    // Sorting options
    if (!empty($_GET['sort_by'])) {
        $criteria['sort_by'] = $_GET['sort_by'];
    }
    
    if (!empty($_GET['sort_dir'])) {
        $criteria['sort_dir'] = $_GET['sort_dir'];
    }
    
    $results = $controller->execute($criteria);
}
?>

<h2>Find Cleaners</h2>

<!-- Search Form -->
<div class="search-container">
    <h3>Find Cleaners</h3>
    <form method="get" action="search_available_cleaners.php">
        <div class="search-row">
            <input type="text" name="keyword" placeholder="Search by name, type or description..."
                   value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <button type="submit" class="btn-search">Search</button>
        </div>
        
        <div class="filter-container">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="service_type">Service Type:</label>
                    <select id="service_type" name="service_type">
                        <option value="">All Types</option>
                        <?php
                        // Get all service types
                        $db = \Config\Database::getConnection();
                        $typesSql = "SELECT DISTINCT type FROM cleaner_services ORDER BY type";
                        $typesResult = mysqli_query($db, $typesSql);
                        while ($type = mysqli_fetch_assoc($typesResult)) {
                            $selected = (isset($_GET['service_type']) && $_GET['service_type'] === $type['type']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($type['type']) . '" ' . $selected . '>' . htmlspecialchars($type['type']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="price_min">Min Price:</label>
                    <input type="number" id="price_min" name="price_min" min="0" step="0.01" 
                           value="<?= isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '' ?>">
                </div>
                
                <div class="filter-group">
                    <label for="price_max">Max Price:</label>
                    <input type="number" id="price_max" name="price_max" min="0" step="0.01"
                           value="<?= isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '' ?>">
                </div>
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label for="availability">Availability:</label>
                    <input type="text" id="availability" name="availability" 
                           value="<?= isset($_GET['availability']) ? htmlspecialchars($_GET['availability']) : '' ?>" 
                           placeholder="e.g. weekends, mornings">
                </div>
                
                <div class="filter-group">
                    <label for="sort_by">Sort By:</label>
                    <select id="sort_by" name="sort_by">
                        <option value="">Default (Price Low to High)</option>
                        <option value="price" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'price') ? 'selected' : '' ?>>Price</option>
                        <option value="name" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'name') ? 'selected' : '' ?>>Service Name</option>
                        <option value="type" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] === 'type') ? 'selected' : '' ?>>Service Type</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sort_dir">Sort Direction:</label>
                    <select id="sort_dir" name="sort_dir">
                        <option value="asc" <?= (isset($_GET['sort_dir']) && $_GET['sort_dir'] === 'asc') ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= (isset($_GET['sort_dir']) && $_GET['sort_dir'] === 'desc') ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-primary">Apply Filters</button>
                <a href="search_available_cleaners.php" class="btn-secondary">Reset</a>
            </div>
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

.filter-container {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 15px;
    margin-bottom: 20px;
}

.filter-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.filter-group {
    flex: 1;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.filter-group select,
.filter-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn-primary,
.btn-secondary {
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
}

@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
    }
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
