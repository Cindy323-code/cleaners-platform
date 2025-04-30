<?php
// boundary/profile/update_user_profile.php
namespace Boundary;

use Controller\UpdateUserProfileController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/UpdateUserProfileController.php';

if (!isset($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    $fields = [];
    if ($_POST['fullName'])    $fields['fullName'] = trim($_POST['fullName']);
    if ($_POST['avatarUrl'])   $fields['avatarUrl'] = trim($_POST['avatarUrl']);
    if ($_POST['bio'])         $fields['bio']       = trim($_POST['bio']);
    if ($_POST['availability'])$fields['availability']=trim($_POST['availability']);

    $ok = (new UpdateUserProfileController())->execute($userId, $fields);
    $message = $ok ? 'Profile updated' : 'Update failed';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Update Profile</title></head>
<body>
  <h2>Update Profile</h2>
  <?php if ($message): ?><p><?=htmlspecialchars($message)?></p><?php endif; ?>
  <form method="post">
    <label>Name: <input name="fullName"></label><br>
    <label>Avatar URL: <input name="avatarUrl"></label><br>
    <label>Bio: <textarea name="bio"></textarea></label><br>
    <label>Availability: <input name="availability"></label><br>
    <button type="submit">Update</button>
  </form>
</body>
</html>
