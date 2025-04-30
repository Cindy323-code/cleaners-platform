<?php
namespace Boundary;

use Controller\ViewCleanerProfileController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/ViewCleanerProfileController.php';

$cleaner   = null;
$cleanerId = intval($_GET['id'] ?? 0);

if ($cleanerId) {
    $cleaner = (new ViewCleanerProfileController())->execute($cleanerId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cleaner Profile</title>
</head>
<body>
  <h2>Cleaner Profile</h2>

  <form method="get">
    <label>Cleaner ID:
      <input name="id" value="<?= $cleanerId ?: '' ?>" required>
    </label>
    <button type="submit">View</button>
  </form>

  <?php if ($cleaner): ?>
    <hr>
    <ul>
      <li>ID: <?= htmlspecialchars($cleaner['id']) ?></li>
      <li>Username: <?= htmlspecialchars($cleaner['username']) ?></li>
      <li>Profile Info: <?= htmlspecialchars($cleaner['profile']) ?></li>
      <li>Bio: <?= htmlspecialchars($cleaner['bio']) ?></li>
    </ul>
  <?php elseif ($cleanerId): ?>
    <p>No cleaner found with that ID.</p>
  <?php endif; ?>
</body>
</html>
