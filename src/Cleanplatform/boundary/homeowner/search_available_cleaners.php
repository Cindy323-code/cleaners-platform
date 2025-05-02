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
            <button type="submit" class="btn">Search</button>
            <a href="search_available_cleaners.php" class="btn btn-secondary">Show All</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title"><?= empty($searchTerm) ? 'All Available Cleaners' : 'Search Results' ?></div>
    
    <?php if (empty($results)): ?>
        <div class="notice">
            <p>No cleaners found. Please try another search term.</p>
        </div>
    <?php else: ?>
        <div class="cleaners-grid">
            <?php foreach ($results as $cleaner): ?>
                <div class="cleaner-card">
                    <div class="cleaner-header">
                        <h3><?= htmlspecialchars($cleaner['username']) ?></h3>
                        <?php if (!empty($cleaner['full'])): ?>
                            <p class="cleaner-fullname"><?= htmlspecialchars($cleaner['full']) ?></p>
                        <?php endif; ?>
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
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.form-actions {
    margin-top: 15px;
}
.btn {
    background: #4285f4;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    padding: 10px 15px;
    text-decoration: none;
}
.btn-small {
    padding: 5px 10px;
    font-size: 12px;
}
.btn-secondary {
    background: #6c757d;
}
.notice {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #4285f4;
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
}
.cleaner-header {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #eee;
}
.cleaner-header h3 {
    margin: 0 0 5px 0;
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
    background: #f0f8ff;
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}
.service-info h4 {
    margin-top: 0;
    margin-bottom: 5px;
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
}
.cleaner-actions form {
    display: inline;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
