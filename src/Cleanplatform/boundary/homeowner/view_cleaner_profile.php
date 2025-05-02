<?php
namespace Boundary;

use Controller\ViewCleanerProfileController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/ViewCleanerProfileController.php';
require_once __DIR__ . '/../../controller/SearchCleaningServicesController.php';

$cleaner = null;
$services = [];
$cleanerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cleanerId) {
    // 获取清洁工档案
    $cleaner = (new ViewCleanerProfileController())->execute($cleanerId);
    
    // 获取清洁工的服务
    if ($cleaner) {
        $db = \Config\Database::getConnection();
        $sql = 'SELECT id, name, type, price, description 
                FROM cleaner_services 
                WHERE cleaner_id = ?';
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="container">
    <h2>Cleaner Profile</h2>

    <!-- 添加ID搜索表单 -->
    <div class="search-form">
        <form method="get" action="view_cleaner_profile.php">
            <div class="form-group">
                <label for="id">Find cleaner by ID:</label>
                <div class="input-group">
                    <input type="number" id="id" name="id" placeholder="Enter Cleaner ID" value="<?= $cleanerId ?>" required>
                    <button type="submit" class="btn">Search</button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($cleaner): ?>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($cleaner['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($cleaner['avatar_url']) ?>" alt="Profile Picture">
                <?php else: ?>
                    <div class="avatar-placeholder"><?= htmlspecialchars(substr($cleaner['username'], 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h2><?= htmlspecialchars($cleaner['username']) ?></h2>
                <?php if (!empty($cleaner['full'])): ?>
                    <h3><?= htmlspecialchars($cleaner['full']) ?></h3>
                <?php endif; ?>
                <p class="profile-id">Cleaner ID: <?= htmlspecialchars($cleaner['id']) ?></p>
                <?php if (!empty($cleaner['status'])): ?>
                    <span class="profile-status <?= $cleaner['status'] === 'active' ? 'active' : 'inactive' ?>">
                        <?= htmlspecialchars(ucfirst($cleaner['status'])) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-content">
            <div class="profile-section">
                <h3>About</h3>
                <?php if (!empty($cleaner['bio'])): ?>
                    <p><?= htmlspecialchars($cleaner['bio']) ?></p>
                <?php else: ?>
                    <p class="empty-notice">No bio available</p>
                <?php endif; ?>
                
                <?php if (!empty($cleaner['availability'])): ?>
                    <h4>Availability</h4>
                    <p><?= htmlspecialchars($cleaner['availability']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($cleaner['email'])): ?>
                    <h4>Contact</h4>
                    <p>Email: <?= htmlspecialchars($cleaner['email']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($cleaner['rating'])): ?>
                    <h4>Rating</h4>
                    <p><?= htmlspecialchars($cleaner['rating']) ?> / 5</p>
                <?php endif; ?>
            </div>
            
            <div class="profile-section">
                <h3>Services (<?= count($services) ?>)</h3>
                <?php if (empty($services)): ?>
                    <p class="empty-notice">No services available</p>
                <?php else: ?>
                    <div class="services-grid">
                        <?php foreach ($services as $service): ?>
                            <div class="service-card">
                                <div class="service-header">
                                    <h4><?= htmlspecialchars($service['name']) ?></h4>
                                    <span class="service-type"><?= htmlspecialchars($service['type']) ?></span>
                                </div>
                                <div class="service-price">$<?= htmlspecialchars($service['price']) ?></div>
                                <div class="service-id">Service ID: <?= htmlspecialchars($service['id']) ?></div>
                                <?php if (!empty($service['description'])): ?>
                                    <div class="service-description">
                                        <?= htmlspecialchars($service['description']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="service-actions">
                                    <form action="/Cleanplatform/boundary/shortlist/add_to_shortlist.php" method="post">
                                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                        <button type="submit" class="btn btn-small">Add to Shortlist</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-footer">
            <a href="search_available_cleaners.php" class="btn">&laquo; Back to Cleaners</a>
        </div>
    </div>

    <?php else: ?>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h3>Cleaner Not Found</h3>
        <p>The cleaner profile you requested could not be found.</p>
        <a href="search_available_cleaners.php" class="btn">Browse Cleaners</a>
    </div>
    <?php endif; ?>
</div>

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.search-form {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-group {
    display: flex;
}

.input-group input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px 0 0 4px;
}

.input-group button {
    border-radius: 0 4px 4px 0;
}

.profile-container, .error-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.profile-header {
    padding: 30px;
    background: linear-gradient(to right, #4285f4, #34a853);
    color: white;
    display: flex;
    align-items: center;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 20px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    font-size: 40px;
    color: #4285f4;
    text-transform: uppercase;
}

.profile-info h2 {
    margin: 0 0 5px 0;
    font-size: 24px;
}

.profile-info h3 {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: normal;
}

.profile-id {
    margin: 0 0 10px 0;
    font-size: 14px;
    opacity: 0.8;
}

.profile-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
    text-transform: uppercase;
}

.profile-status.active {
    background: #34a853;
    color: white;
}

.profile-status.inactive {
    background: #ea4335;
    color: white;
}

.profile-content {
    padding: 30px;
}

.profile-section {
    margin-bottom: 30px;
}

.profile-section h3 {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-top: 0;
    color: #4285f4;
}

.empty-notice {
    color: #888;
    font-style: italic;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 20px;
}

.service-card {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 15px;
    position: relative;
    transition: all 0.2s;
}

.service-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.service-header {
    margin-bottom: 10px;
}

.service-header h4 {
    margin: 0 0 5px 0;
}

.service-type {
    display: inline-block;
    background: #f1f3f4;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
    color: #5f6368;
}

.service-price {
    font-size: 20px;
    font-weight: bold;
    color: #34a853;
    margin-bottom: 5px;
}

.service-id {
    font-size: 12px;
    color: #5f6368;
    margin-bottom: 10px;
}

.service-description {
    font-size: 14px;
    color: #5f6368;
    margin-bottom: 15px;
}

.service-actions {
    margin-top: 10px;
}

.profile-footer {
    padding: 20px 30px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
}

.btn {
    background: #4285f4;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.btn-small {
    padding: 5px 10px;
    font-size: 12px;
}

.error-container {
    text-align: center;
    padding: 60px 30px;
}

.error-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.error-container h3 {
    margin: 0 0 10px 0;
    color: #ea4335;
}

.error-container p {
    color: #5f6368;
    margin-bottom: 30px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
