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

$username = $_GET['username'] ?? '';
$userData = null;
if ($username) {
    $controller = new ViewUserAccountController();
    $userData = $controller->execute($username);
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>View User Account</title></head>
<body>
  <h2>View User Account</h2>
  <form method="get">
    <label for="username">Username:
      <input type="text" id="username" name="username" value="<?=htmlspecialchars($username)?>" required>
    </label>
    <button type="submit">Search</button>
  </form>
  <?php if ($userData): ?>
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
</body>
</html>
