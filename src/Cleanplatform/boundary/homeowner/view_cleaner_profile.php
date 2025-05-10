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
    // 获取清洁工档案和服务信息
    $cleaner = (new ViewCleanerProfileController())->execute($cleanerId);
    
    // 服务信息已包含在cleaner数据中
    if ($cleaner && isset($cleaner['services'])) {
        $services = $cleaner['services'];
    }
}
?>

<h2>Cleaner Profile</h2>

<!-- Search Form -->
<div class="card">
    <div class="card-title">Find a Cleaner</div>
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
<!-- Profile Information -->
<div class="card">
    <div class="card-title">Profile Information</div>
    <div class="profile-content">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($cleaner['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($cleaner['avatar_url']) ?>" alt="Profile Picture">
                <?php else: ?>
                    <?php 
                        // 使用全名或用户名的第一个字母作为占位符
                        $initials = !empty($cleaner['full_name']) ? 
                            substr($cleaner['full_name'], 0, 1) : 
                            substr($cleaner['username'], 0, 1);
                    ?>
                    <div class="avatar-placeholder"><?= htmlspecialchars($initials) ?></div>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <h2><?= htmlspecialchars(!empty($cleaner['full_name']) ? $cleaner['full_name'] : $cleaner['username']) ?></h2>
                <p class="profile-id">Cleaner ID: <?= htmlspecialchars($cleaner['id']) ?></p>
                <?php if (!empty($cleaner['status'])): ?>
                    <span class="profile-status <?= $cleaner['status'] === 'active' ? 'active' : 'inactive' ?>">
                        <?= htmlspecialchars(ucfirst($cleaner['status'])) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

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
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="card">
    <div class="card-title">Services (<?= count($services) ?>)</div>
    <?php if (empty($services)): ?>
        <div class="empty-state">
            <div class="empty-icon">🧹</div>
            <h3>No Services Available</h3>
            <p>This cleaner hasn't added any services yet.</p>
        </div>
    <?php else: ?>
        <div class="cleaners-grid">
            <?php foreach ($services as $service): ?>
                <div class="cleaner-card">
                    <div class="cleaner-header">
                        <h3><?= htmlspecialchars($service['name']) ?></h3>
                        <span class="service-type"><?= htmlspecialchars($service['type']) ?></span>
                    </div>
                    
                    <div class="cleaner-content">
                        <p class="service-price">Price: $<?= htmlspecialchars($service['price']) ?></p>
                        <p class="service-id">Service ID: <?= htmlspecialchars($service['id']) ?></p>
                        
                        <?php if (!empty($service['description'])): ?>
                            <div class="service-info">
                                <div class="service-description">Description: <?= htmlspecialchars($service['description']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cleaner-actions">
                        <form action="/Cleanplatform/boundary/shortlist/add_to_shortlist.php" method="post">
                            <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                            <button type="submit" class="btn btn-small">Add to Shortlist</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="button-group">
        <a href="search_available_cleaners.php" class="btn">&laquo; Back to Cleaners</a>
    </div>
</div>

<?php else: ?>
<!-- Error State -->
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">⚠️</div>
        <h3>Cleaner Not Found</h3>
        <p>The cleaner profile you requested could not be found.</p>
        <a href="search_available_cleaners.php" class="btn">Browse Cleaners</a>
    </div>
</div>
<?php endif; ?>

<style>
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
.profile-header {
    padding: 20px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #eee;
}
.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 20px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.avatar-placeholder {
    font-size: 30px;
    color: var(--primary-color);
    text-transform: uppercase;
}
.profile-info h2 {
    margin: 0 0 5px 0;
    font-size: 24px;
    color: var(--primary-color);
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
    padding: 0;
}
.profile-section {
    margin-bottom: 20px;
    padding: 15px;
}
.profile-section h3 {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-top: 0;
    color: var(--primary-color);
    font-size: 18px;
}
.profile-section h4 {
    color: #555;
    margin: 15px 0 5px;
    font-size: 16px;
}
.empty-notice {
    color: #888;
    font-style: italic;
}
.cleaners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 20px;
    margin: 20px 0;
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
.cleaner-content {
    padding: 15px;
}
.service-type {
    display: inline-block;
    background: rgba(66, 133, 244, 0.1);
    color: var(--primary-color);
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
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
    color: #5f6368;
}
.service-info {
    background: rgba(0, 123, 255, 0.05);
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}
.service-description {
    font-size: 14px;
    color: #5f6368;
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
    width: 100%;
}
.cleaner-actions .btn,
.cleaner-actions button.btn {
    height: 38px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
}
.button-group {
    padding: 15px;
    text-align: center;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    margin-top: 10px;
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
.empty-state .btn {
    margin-top: 10px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
