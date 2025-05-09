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
    
    <div class="profile-info">
        <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($userRole)) ?></p>
        
        <?php if($profile): ?>
            <?php if(isset($profile['full_name'])): ?>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($profile['full_name']) ?></p>
            <?php endif; ?>
            
            <?php if(isset($profile['bio']) && !empty($profile['bio'])): ?>
                <p><strong>Bio:</strong> <?= htmlspecialchars($profile['bio']) ?></p>
            <?php endif; ?>
            
            <?php if(isset($profile['availability']) && !empty($profile['availability'])): ?>
                <p><strong>Availability:</strong> <?= htmlspecialchars($profile['availability']) ?></p>
            <?php endif; ?>
            
            <p><strong>Profile Status:</strong> 
                <span class="status-badge status-<?= htmlspecialchars(strtolower($profile['status'])) ?>">
                    <?= htmlspecialchars($profile['status']) ?>
                </span>
            </p>
            
            <?php if(isset($profile['updated_at'])): ?>
                <p><strong>Last Updated:</strong> <?= htmlspecialchars($profile['updated_at']) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <div class="notice">
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
            <?php if($profile['status'] === 'active'): ?>
                <a href="deactivate_user_profile.php" class="btn">Deactivate Profile</a>
            <?php else: ?>
                <a href="update_user_profile.php?action=activate" class="btn">Activate Profile</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="create_user_profile.php" class="btn">Create Profile</a>
        <?php endif; ?>
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
.profile-info {
    margin-bottom: 20px;
}
.profile-info p {
    margin: 10px 0;
}
.button-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.notice {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #4285f4;
    margin: 15px 0;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
