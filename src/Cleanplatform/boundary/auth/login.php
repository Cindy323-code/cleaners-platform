<?php
namespace Boundary;
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../partials/header.php';
use Config\Database;
use Controller\UserLoginController;
use Controller\CleanerLoginController;
use Controller\HomeownerLoginController;
use Controller\PlatformManagerLoginController;

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role     = $_POST['role']     ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    switch ($role) {
        case 'admin':
            $controller = new UserLoginController();
            break;
        case 'cleaner':
            $controller = new CleanerLoginController();
            break;
        case 'homeowner':
            $controller = new HomeownerLoginController();
            break;
        case 'manager':
            $controller = new PlatformManagerLoginController();
            break;
        default:
            $controller   = null;
            $errorMessage = 'Please select a valid role.';
    }

    if ($controller) {
        $userInfo = $controller->execute($username, $password);
        if ($userInfo) {
            $_SESSION['user'] = $userInfo;
            $_SESSION['role'] = $role;
            header('Location: /Cleanplatform/public/dashboard.php');
            exit;
        } else {
            $errorMessage = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unified Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        label, select, input { display: block; margin: 0.5em 0; }
        button { margin-top: 1em; padding: 0.5em 1em; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>System Login</h2>
    <?php if ($errorMessage): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">User Admin</option>
            <option value="cleaner">Cleaner</option>
            <option value="homeowner">Home Owner</option>
            <option value="manager">Platform Manager</option>
        </select>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>
</html>
