<?php
namespace Boundary;

use Controller\DeactivateUserProfileController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/DeactivateUserProfileController.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId  = $_SESSION['user']['id'];
    $ok      = (new DeactivateUserProfileController())->execute($userId);
    $message = $ok ? 'Profile deactivated.' : 'Operation failed.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Deactivate Profile</title></head>
<body>
  <h2>Deactivate Profile</h2>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <form method="post">
    <button type="submit" onclick="return confirm('Are you sure?')">Deactivate</button>
  </form>
</body>
</html>
