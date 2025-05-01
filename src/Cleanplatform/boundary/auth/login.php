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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleaning Platform - Login</title>
    <link rel="stylesheet" href="/Cleanplatform/public/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .login-container {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .login-header h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Cleaning Platform Management System</h2>
            <p>Please login to your account</p>
        </div>
        
        <?php if ($errorMessage): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="role">User Role:</label>
                <select name="role" id="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">System Administrator</option>
                    <option value="cleaner">Cleaner</option>
                    <option value="homeowner">Homeowner</option>
                    <option value="manager">Platform Manager</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
            
            <div class="form-footer">
                © <?= date('Y') ?> Cleaning Platform - All Rights Reserved
            </div>
        </form>
    </div>
</body>
</html>
