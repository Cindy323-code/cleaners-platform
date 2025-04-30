<?php
// boundary/admin/update_user_account.php
namespace Boundary;

use Controller\UpdateUserAccountController;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/UpdateUserAccountController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

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
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Update User Account</title></head>
<body>
  <h2>Update User Account</h2>
  <?php if ($message): ?><p><?=htmlspecialchars($message)?></p><?php endif; ?>
  <form method="post">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Email:    <input type="email" name="email"></label><br>
    <label>Role:     <input type="text" name="role"></label><br>
    <label>Status:   <select name="status">
        <option value="active">active</option>
        <option value="suspended">suspended</option>
      </select>
    </label><br>
    <button type="submit">Update</button>
  </form>
</body>
</html>