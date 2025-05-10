<?php
// boundary/admin/view_user_account.php
namespace Boundary;

use Controller\ViewUserAccountController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/ViewUserAccountController.php';

// Ensure user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

// Get all users for the list view
$controller = new ViewUserAccountController();
$allUsers = $controller->getAllUsers();

// Process single user view if username provided
$username = $_GET['username'] ?? '';
$userData = null;
if ($username) {
    $userData = $controller->execute($username);
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>View User Account</title></head>
<body>
  <h2>View User Account</h2>

  <!-- User Search Form -->
  <div class="card">
    <div class="card-title">Search User</div>
    <form method="get">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?=htmlspecialchars($username)?>" required>
      </div>
      <button type="submit" class="btn">Search</button>
    </form>

    <?php if ($userData): ?>
      <h3>User Details</h3>
      <ul>
        <li>ID: <?=htmlspecialchars($userData['id'])?></li>
        <li>Username: <?=htmlspecialchars($userData['user'])?></li>
        <li>Email: <?=htmlspecialchars($userData['email'])?></li>
        <li>Role: <?=htmlspecialchars($userData['role'])?></li>
        <li>Status: <?=htmlspecialchars($userData['status'])?></li>
        <li>Created At: <?=htmlspecialchars($userData['createdAt'])?></li>
      </ul>
    <?php elseif ($username): ?>
      <p>No user found.</p>
    <?php endif; ?>
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
                <a href="?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">View</a>
                <a href="update_user_account.php?username=<?=htmlspecialchars($user['user'])?>" class="btn btn-small">Update</a>
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
</body>
</html>
