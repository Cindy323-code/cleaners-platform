<?php
// boundary/profile/view_user_profile.php
namespace Boundary;

use Controller\ViewUserProfileController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/ViewUserProfileController.php';

// 检查用户是否已登录（允许任何角色）
if(!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

// 获取当前用户ID和角色
$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['role'];
$username = $_SESSION['user']['username'];

// 初始化控制器
$controller = new ViewUserProfileController();

// 获取用户资料
$profile = $controller->execute($userId, $userRole);
?>

<h2>My Profile</h2>

<div class="card">
    <div class="card-title">User Information</div>
    
    <div class="profile-content">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if($profile && !empty($profile['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($profile['avatar_url']) ?>" alt="Profile Picture">
                <?php else: ?>
                    <?php 
                        // Use the first initial of username or full name as avatar placeholder
                        $initial = ($profile && !empty($profile['full_name'])) ? 
                            substr($profile['full_name'], 0, 1) : 
                            substr($username, 0, 1);
                    ?>
                    <div class="avatar-placeholder"><?= htmlspecialchars(strtoupper($initial)) ?></div>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <h3><?= htmlspecialchars(($profile && !empty($profile['full_name'])) ? $profile['full_name'] : $username) ?></h3>
                <p class="profile-role"><?= htmlspecialchars(ucfirst($userRole)) ?></p>
            </div>
        </div>
        
        <?php if($profile): ?>
            <div class="profile-section">
                <h3>About</h3>
                
                <?php if(isset($profile['bio']) && !empty($profile['bio'])): ?>
                    <p><?= htmlspecialchars($profile['bio']) ?></p>
                <?php else: ?>
                    <p class="empty-notice">No bio available</p>
                <?php endif; ?>
                
                <?php if(isset($profile['availability']) && !empty($profile['availability'])): ?>
                    <h4>Availability</h4>
                    <p><?= htmlspecialchars($profile['availability']) ?></p>
                <?php endif; ?>
                
                <?php if(isset($profile['updated_at'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Last Updated:</div>
                        <div class="detail-value"><?= htmlspecialchars($profile['updated_at']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">👤</div>
                <h3>Profile Not Created</h3>
                <p>You haven't created your profile yet.</p>
                <a href="create_user_profile.php" class="btn">Create Profile</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-title">Profile Actions</div>
    <div class="button-group">
        <?php if($profile): ?>
            <a href="update_user_profile.php" class="btn">Update Profile</a>
        <?php else: ?>
            <a href="create_user_profile.php" class="btn">Create Profile</a>
        <?php endif; ?>
        <a href="/Cleanplatform/public/dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
</div>

<?php if($userRole === 'cleaner'): ?>
<div class="card">
    <div class="card-title">Cleaner Services</div>
    <p>Manage your cleaning services and view statistics:</p>
    <div class="button-group">
        <a href="../service/manage_cleaning_services.php?tab=view" class="btn">View My Services</a>
        <a href="../service/manage_cleaning_services.php?tab=create" class="btn">Add New Service</a>
        <a href="../history/view_service_profile_views.php" class="btn">View Profile Statistics</a>
    </div>
</div>
<?php elseif($userRole === 'homeowner'): ?>
<div class="card">
    <div class="card-title">Homeowner Tools</div>
    <p>Manage your shortlist and view service history:</p>
    <div class="button-group">
        <a href="../shortlist/view_shortlist.php" class="btn">View My Shortlist</a>
        <a href="../homeowner/search_available_cleaners.php" class="btn">Find Cleaners</a>
        <a href="../history/service_usage_history.php" class="btn">View Service History</a>
    </div>
</div>
<?php endif; ?>

<style>
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
.profile-info h3 {
    margin: 0 0 5px 0;
    font-size: 24px;
    color: var(--primary-color);
}
.profile-role {
    font-size: 16px;
    color: #5f6368;
    margin: 0;
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
.detail-item {
    display: flex;
    padding: 8px 0;
    border-top: 1px solid #eee;
    margin-top: 15px;
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
    flex-wrap: wrap;
    padding: 15px;
}
.empty-notice {
    color: #888;
    font-style: italic;
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
