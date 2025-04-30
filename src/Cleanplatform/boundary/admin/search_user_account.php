<?php
// boundary/admin/search_user_account.php
namespace Boundary;
require_once __DIR__ . '/../partials/header.php';
use Controller\SearchUserAccountController;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/SearchUserAccountController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
    $controller = new SearchUserAccountController();
    $results = $controller->execute($keyword);
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Search User Accounts</title></head>
<body>
  <h2>Search User Accounts</h2>
  <form method="get">
    <label>Keyword: <input type="text" name="keyword" required></label>
    <button type="submit">Search</button>
  </form>
  <?php if ($results): ?>
    <table border="1">
      <tr><th>Username</th><th>Role</th><th>Email</th><th>Status</th></tr>
      <?php foreach ($results as $row): ?>
        <tr>
          <td><?=htmlspecialchars($row['username'])?></td>
          <td><?=htmlspecialchars($row['role'])?></td>
          <td><?=htmlspecialchars($row['email'])?></td>
          <td><?=htmlspecialchars($row['status'])?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
