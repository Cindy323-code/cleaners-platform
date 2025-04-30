<?php
// boundary/admin/suspend_user_account.php
namespace Boundary;

use Controller\SuspendUserAccountController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/SuspendUserAccountController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $controller = new SuspendUserAccountController();
    $message = $controller->execute($username) ? 'User suspended' : 'Operation failed';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Suspend User Account</title></head>
<body>
  <h2>Suspend User Account</h2>
  <?php if ($message): ?><p><?=htmlspecialchars($message)?></p><?php endif; ?>
  <form method="post">
    <label>Username: <input type="text" name="username" required></label><br>
    <button type="submit">Suspend</button>
  </form>
</body>
</html>