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
    if ($_POST['email'])    $fields['email'] = trim($_POST['email']);
    if ($_POST['role'])     $fields['role'] = trim($_POST['role']);
    if ($_POST['status'])   $fields['status'] = trim($_POST['status']);

    $controller = new UpdateUserAccountController();
    $ok = $controller->execute($username, $fields);
    $message = $ok ? 'Update successful' : 'Update failed';
    
    // Refresh the user list after successful update
    if ($ok) {
        $allUsers = $viewController->getAllUsers();
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
      <input type="text" id="username" name="username" value="<?=htmlspecialchars($preUserData['user'] ?? $preUsername)?>" required>
    </div>
    
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?=htmlspecialchars($preUserData['email'] ?? '')?>">
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
                <a href="suspend_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">Suspend</a>
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