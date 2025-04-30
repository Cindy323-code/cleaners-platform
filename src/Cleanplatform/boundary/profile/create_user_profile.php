<?php
namespace Boundary;

use Controller\CreateUserProfileController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/CreateUserProfileController.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    $data   = [
        'fullName'     => trim($_POST['fullName']),
        'avatarUrl'    => trim($_POST['avatarUrl']),
        'bio'          => trim($_POST['bio']),
        'availability' => trim($_POST['availability']),
        'status'       => 'active'
    ];
    $ok      = (new CreateUserProfileController())->execute($userId, $data);
    $message = $ok ? 'Profile created.' : 'Failed to create profile.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Create Profile</title></head>
<body>
  <h2>Create Profile</h2>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Name: <input name="fullName" required></label><br>
    <label>Avatar URL: <input name="avatarUrl"></label><br>
    <label>Bio: <textarea name="bio"></textarea></label><br>
    <label>Availability: <input name="availability"></label><br>
    <button type="submit">Create</button>
  </form>
</body>
</html>
