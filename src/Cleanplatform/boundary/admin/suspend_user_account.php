<?php
// boundary/admin/suspend_user_account.php
namespace Boundary;

use Controller\SuspendUserAccountController;
use Controller\ViewUserAccountController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/SuspendUserAccountController.php';
require_once __DIR__ . '/../../controller/ViewUserAccountController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

// Get all users for the list view
$viewController = new ViewUserAccountController();
$allUsers = $viewController->getAllUsers();

// Handle username from GET (for pre-filling form)
$preUsername = $_GET['username'] ?? '';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $controller = new SuspendUserAccountController();
    $ok = $controller->execute($username);
    $message = $ok ? 'User suspended successfully' : 'Operation failed';
    
    // Refresh the user list after successful suspension
    if ($ok) {
        $allUsers = $viewController->getAllUsers();
    }
}
?>

<h2>Suspend User Account</h2>

<!-- Suspend User Form -->
<div class="card">
  <div class="card-title">Suspend User</div>
  <?php if ($message): ?>
    <div class="<?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
      <?=htmlspecialchars($message)?>
    </div>
  <?php endif; ?>
  
  <form method="post">
    <div class="form-group">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?=htmlspecialchars($preUsername)?>" required>
    </div>
    
    <div class="form-group">
      <p class="warning">Warning: Suspending a user will prevent them from accessing the system. This action can be reversed by updating their status back to active.</p>
    </div>
    
    <button type="submit" class="btn">Suspend User</button>
  </form>
</div>

<!-- User List Table -->
<div class="card">
  <div class="card-title">Active Users</div>
  <?php 
    // Filter to show only active users
    $activeUsers = array_filter($allUsers, function($user) {
      return $user['status'] === 'active';
    });
    
    if (!empty($activeUsers)): 
  ?>
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
        <?php foreach ($activeUsers as $user): ?>
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
              <a href="?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">Select for Suspension</a>
              <a href="view_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">View</a>
              <a href="update_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">Update</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No active users found.</p>
  <?php endif; ?>
</div>

<!-- Suspended Users List -->
<div class="card">
  <div class="card-title">Suspended Users</div>
  <?php 
    // Filter to show only suspended users
    $suspendedUsers = array_filter($allUsers, function($user) {
      return $user['status'] === 'suspended';
    });
    
    if (!empty($suspendedUsers)): 
  ?>
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
        <?php foreach ($suspendedUsers as $user): ?>
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
              <a href="view_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">View</a>
              <a href="update_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">Reactivate</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No suspended users found.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>