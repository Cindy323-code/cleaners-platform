<?php
// boundary/profile/update_user_profile.php
namespace Boundary;

use Controller\UpdateUserProfileController;
use Controller\ViewUserProfileController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/UpdateUserProfileController.php';
require_once __DIR__ . '/../../controller/ViewUserProfileController.php';

if (!isset($_SESSION['user'])) { 
    header('Location: ../auth/login.php'); 
    exit; 
}

// Get current user data
$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['role'];

// Get current profile data to pre-fill the form
$profileController = new ViewUserProfileController();
$profile = $profileController->execute($userId, $userRole);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'full_name' => !empty($_POST['full_name']) ? trim($_POST['full_name']) : '',
        'avatar_url' => !empty($_POST['avatar_url']) ? trim($_POST['avatar_url']) : '',
        'bio' => !empty($_POST['bio']) ? trim($_POST['bio']) : '',
        'availability' => !empty($_POST['availability']) ? trim($_POST['availability']) : ''
    ];

    $ok = (new UpdateUserProfileController())->execute($userId, $fields);
    if ($ok) {
        $message = 'Profile updated successfully';
        $messageType = 'success';
        // Refresh profile data after update
        $profile = $profileController->execute($userId, $userRole);
    } else {
        $message = 'Failed to update profile';
        $messageType = 'error';
    }
}
?>

<h2>Update Profile</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-title">Edit Your Profile</div>
    
    <form method="post" class="profile-form">
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" class="form-control" 
                   value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="avatar_url">Avatar URL:</label>
            <input type="text" id="avatar_url" name="avatar_url" class="form-control" 
                   value="<?= htmlspecialchars($profile['avatar_url'] ?? '') ?>">
            <div class="field-hint">Enter a URL to an image for your profile picture</div>
        </div>
        
        <div class="form-group">
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" class="form-control" rows="4"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            <div class="field-hint">Tell others about yourself</div>
        </div>
        
        <div class="form-group">
            <label for="availability">Availability:</label>
            <textarea id="availability" name="availability" class="form-control" rows="3"><?= htmlspecialchars($profile['availability'] ?? '') ?></textarea>
            <div class="field-hint">Describe when you're available for work</div>
        </div>
        
        <div class="button-group">
            <button type="submit" class="btn">Update Profile</button>
            <a href="view_user_profile.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    border-left: 4px solid;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #28a745;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #dc3545;
}
.profile-form {
    padding: 20px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    color: #333;
}
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 16px;
}
textarea.form-control {
    resize: vertical;
}
.field-hint {
    margin-top: 5px;
    font-size: 14px;
    color: #6c757d;
}
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
