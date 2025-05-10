<?php
namespace Boundary;

use Controller\CreateUserProfileController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/CreateUserProfileController.php';

$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['role'];
$username = $_SESSION['user']['username'];

// Check user role, only cleaner and homeowner can create profiles
if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
    echo "<div class='alert alert-error'>Only cleaners and homeowners can create profiles</div>";
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name'    => trim($_POST['full_name']),
        'avatar_url'   => trim($_POST['avatar_url']),
        'bio'          => trim($_POST['bio']),
        'availability' => trim($_POST['availability']),
        'user_type'    => $userRole
    ];
    
    $controller = new CreateUserProfileController();
    $ok = $controller->execute($userId, $data);
    
    if ($ok) {
        $message = 'Profile created successfully.';
        $messageType = 'success';
        // Redirect to view profile after successful creation
        header('Location: view_user_profile.php');
        exit;
    } else {
        $message = 'Failed to create profile.';
        $messageType = 'error';
    }
}
?>

<h2>Create User Profile</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-title">Create Your Profile</div>
    
    <div class="profile-content">
        <div class="profile-header">
            <div class="profile-avatar">
                <div class="avatar-placeholder"><?= htmlspecialchars(strtoupper(substr($username, 0, 1))) ?></div>
            </div>
            <div class="profile-info">
                <h3><?= htmlspecialchars($username) ?></h3>
                <p class="profile-role"><?= htmlspecialchars(ucfirst($userRole)) ?></p>
            </div>
        </div>
        
        <form method="post" class="profile-form">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" class="form-control" 
                       value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="avatar_url">Avatar URL:</label>
                <input type="text" id="avatar_url" name="avatar_url" class="form-control" 
                       value="<?= isset($_POST['avatar_url']) ? htmlspecialchars($_POST['avatar_url']) : '' ?>">
                <div class="field-hint">Enter a URL to an image for your profile picture</div>
            </div>
            
            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" class="form-control" rows="4"><?= isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : '' ?></textarea>
                <div class="field-hint">Tell others about yourself</div>
            </div>
            
            <div class="form-group">
                <label for="availability">Availability:</label>
                <textarea id="availability" name="availability" class="form-control" rows="3"><?= isset($_POST['availability']) ? htmlspecialchars($_POST['availability']) : '' ?></textarea>
                <div class="field-hint">
                    <?php if ($userRole === 'cleaner'): ?>
                        Describe when you're available to provide cleaning services
                    <?php else: ?>
                        Describe when you typically need cleaning services
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn">Create Profile</button>
                <a href="view_user_profile.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
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
