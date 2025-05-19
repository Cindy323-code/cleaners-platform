<?php
// boundary/admin/update_user_account.php
namespace Boundary;

use Controller\UpdateUserAccountController;
use Controller\ViewUserAccountController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/UpdateUserAccountController.php';
require_once __DIR__ . '/../../controller/ViewUserAccountController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

// Get all users for the list view
$viewController = new ViewUserAccountController();
$allUsers = $viewController->getAllUsers();

// Handle username from GET (for pre-filling form)
$preUsername = $_GET['username'] ?? '';
$preUserData = null;
if ($preUsername) {
    $preUserData = $viewController->execute($preUsername);
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $fields = [];
    if (isset($_POST['email']) && $_POST['email'])    $fields['email'] = trim($_POST['email']);
    if (isset($_POST['role']) && $_POST['role'])     $fields['role'] = trim($_POST['role']);
    if (isset($_POST['status']) && $_POST['status'])   $fields['status'] = trim($_POST['status']);

    // Profile fields
    $profileData = [];
    if (isset($_POST['full_name'])) $profileData['full_name'] = trim($_POST['full_name']);
    if (isset($_POST['avatar_url'])) $profileData['avatar_url'] = trim($_POST['avatar_url']);
    if (isset($_POST['bio'])) $profileData['bio'] = trim($_POST['bio']);
    if (isset($_POST['availability'])) $profileData['availability'] = trim($_POST['availability']);

    $controller = new UpdateUserAccountController();
    $ok = $controller->execute($username, $fields, $profileData);
    $message = $ok ? 'Update successful' : 'Update failed';
    
    // Refresh the user data after successful update to show new profile info
    if ($ok) {
        $allUsers = $viewController->getAllUsers(); // This might need to be re-fetched or ensure it has profile data
        $preUserData = $viewController->execute($username); // Re-fetch current user's data
    }
}
?>

<h2>Update User Account</h2>

<!-- Update User Form -->
<div class="card">
  <div class="card-title">Update User</div>
  <?php if ($message): ?>
    <div class="<?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
      <?=htmlspecialchars($message)?>
    </div>
  <?php endif; ?>
  
  <form method="post">
    <div class="form-group">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?=htmlspecialchars($preUserData['user'] ?? $preUsername)?>" required readonly>
      <!-- Making username readonly as it's the key -->
    </div>
    
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?=htmlspecialchars($preUserData['email'] ?? '')?>">
    </div>

    <div class="form-group">
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" value="<?=htmlspecialchars($preUserData['full_name'] ?? '')?>">
    </div>

    <div class="form-group">
      <label for="avatar_url">Avatar URL:</label>
      <input type="text" id="avatar_url" name="avatar_url" value="<?=htmlspecialchars($preUserData['avatar_url'] ?? '')?>">
    </div>

    <div class="form-group">
      <label for="bio">Bio:</label>
      <textarea id="bio" name="bio" rows="3"><?=htmlspecialchars($preUserData['bio'] ?? '')?></textarea>
    </div>

    <div class="form-group">
      <label for="availability">Availability:</label>
      <input type="text" id="availability" name="availability" value="<?=htmlspecialchars($preUserData['availability'] ?? '')?>">
    </div>
    
    <div class="form-group">
      <label for="role">Role:</label>
      <select name="role" id="role">
        <option value="">-- Select Role --</option>
        <option value="admin" <?= isset($preUserData['role']) && $preUserData['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
        <option value="cleaner" <?= isset($preUserData['role']) && $preUserData['role'] === 'cleaner' ? 'selected' : '' ?>>Cleaner</option>
        <option value="homeowner" <?= isset($preUserData['role']) && $preUserData['role'] === 'homeowner' ? 'selected' : '' ?>>Homeowner</option>
        <option value="manager" <?= isset($preUserData['role']) && $preUserData['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
      </select>
    </div>
    
    <div class="form-group">
      <label for="status">Status:</label>
      <select name="status" id="status">
        <option value="active" <?= isset($preUserData['status']) && $preUserData['status'] === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="suspended" <?= isset($preUserData['status']) && $preUserData['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
      </select>
    </div>
    
    <button type="submit" class="btn">Update User</button>
  </form>
</div>

<!-- User List Table -->
<div class="card">
  <div class="card-title">All Users</div>
  <?php if (!empty($allUsers)): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allUsers as $user): ?>
          <tr>
            <td><?=htmlspecialchars($user['id'])?></td>
            <td><?=htmlspecialchars($user['user'])?></td>
            <td><?=htmlspecialchars($user['email'])?></td>
            <td><?=htmlspecialchars($user['role'])?></td>
            <td>
              <span class="status-badge status-<?=htmlspecialchars(strtolower($user['status']))?>">
                <?=htmlspecialchars($user['status'])?>
              </span>
            </td>
            <td><?=htmlspecialchars($user['createdAt'])?></td>
            <td>
              <a href="?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">Select for Update</a>
              <a href="view_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">View</a>
              <?php if ($user['status'] !== 'suspended'): ?>
                <a href="suspend_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small btn-danger">Suspend</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No users found.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>