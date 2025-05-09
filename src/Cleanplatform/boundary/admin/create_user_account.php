<?php
namespace Boundary;

require_once __DIR__ . '/../partials/header.php';

use Controller\CreateUserAccountController;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/CreateUserAccountController.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username'      => trim($_POST['username']),
        'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'email'         => trim($_POST['email']),
        'role'          => $_POST['role'],
        'status'        => 'active'
    ];
    $controller = new CreateUserAccountController($_POST['role']);
    $ok = $controller->execute($data);
    $message = $ok ? 'User created successfully.' : 'Failed to create user.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create User Account</title>
</head>
<body>
  <h2>Create User Account</h2>
  <?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Username:
      <input name="username" required>
    </label><br>

    <label>Password:
      <input type="password" name="password" required>
    </label><br>

    <label>Email:
      <input type="email" name="email" required>
    </label><br>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
      <option value="admin">User Admin</option>
      <option value="cleaner">Cleaner</option>
      <option value="homeowner">Home Owner</option>
      <option value="manager">Platform Manager</option>
    </select><br>

    <button type="submit">Create</button>
  </form>
</body>
</html>
