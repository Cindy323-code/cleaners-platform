<?php
// ── partials/header.php ─────────────────────────────────────────
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (!isset($_SESSION['user']) && basename($_SERVER['SCRIPT_NAME']) !== 'login.php') {
  header('Location: /Cleanplatform/boundary/auth/login.php');
  exit;
}
$userRole = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['user']['username'] ?? 'Guest';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cleaning Platform</title>
  <style>
      body  { font-family: Arial, sans-serif; margin: 2rem; }
      header{ margin-bottom: 1rem; }
      nav a { margin-right: 1rem; text-decoration:none; }
      .right{ float:right; }
  </style>
</head>
<body>
<header>
  <nav>
    <a href="/Cleanplatform/public/dashboard.php">Dashboard</a>
    <?php if ($userRole !== 'guest'): ?>
      <span class="right">
        Logged in as <strong><?= htmlspecialchars($username) ?></strong> (<?= htmlspecialchars($userRole) ?>)
        | <a href="/Cleanplatform/public/logout.php">Logout</a>
      </span>
    <?php else: ?>
      <span class="right"><a href="/Cleanplatform/boundary/auth/login.php">Login</a></span>
    <?php endif; ?>
  </nav>
  <hr>
</header>
