<?php
// bootstrap.php (which defines BASE_URL) should be included by the script that includes this header.
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Only redirect and show navigation on non-login pages
$isLoginPage = basename($_SERVER['SCRIPT_NAME']) === 'login.php';

// Ensure BASE_URL is defined before using it for redirection or links
if (!defined('BASE_URL')) {
  error_log("Critical Error: BASE_URL not defined in header.php. Check bootstrap.php inclusion in the parent script.");
  // Display a user-friendly error or die, as links and redirects will fail.
  die('Critical configuration error: The website base URL is not set up correctly. Please check server logs or contact support.');
}

if (!isset($_SESSION['user']) && !$isLoginPage) {
  header('Location: ' . BASE_URL . '/boundary/auth/login.php');
  exit;
}
$userRole = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['user']['username'] ?? 'Guest';

// If this is a login page, don't output HTML header
if (!$isLoginPage):
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cleaning Platform</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/filter.css">
</head>
<body>
<div class="container">
  <header class="header">
    <h2>Cleaning Platform Management System</h2>
    <div class="user-info">
      <?php if ($userRole !== 'guest'): ?>
        <span>
          Welcome, <strong><?= htmlspecialchars($username) ?></strong> (<?= htmlspecialchars($userRole) ?>)
          | <a href="<?= BASE_URL ?>/public/logout.php">Logout</a>
        </span>
      <?php else: ?>
        <span><a href="<?= BASE_URL ?>/boundary/auth/login.php">Login</a></span>
      <?php endif; ?>
    </div>
  </header>

  <nav class="nav">
    <a href="<?= BASE_URL ?>/public/dashboard.php">Dashboard</a>
    <?php if ($userRole === 'admin'): ?>
      <a href="<?= BASE_URL ?>/boundary/admin/create_user_account.php">Create User</a>
      <a href="<?= BASE_URL ?>/boundary/admin/view_user_account.php">View Users</a>
      <a href="<?= BASE_URL ?>/boundary/admin/update_user_account.php">Update User</a>
      <a href="<?= BASE_URL ?>/boundary/admin/suspend_user_account.php">Suspend User</a>
      <a href="<?= BASE_URL ?>/boundary/admin/search_user_account.php">Search Users</a>
    <?php elseif ($userRole === 'cleaner'): ?>
      <a href="<?= BASE_URL ?>/boundary/service/manage_cleaning_services.php">Manage Services</a>
      <a href="<?= BASE_URL ?>/boundary/history/view_service_profile_views.php">Profile Views</a>
      <a href="<?= BASE_URL ?>/boundary/history/view_service_shortlist_count.php">Shortlist Count</a>
      <a href="<?= BASE_URL ?>/boundary/history/search_confirmed_matches.php">Matches</a>
      <a href="<?= BASE_URL ?>/boundary/profile/view_user_profile.php">My Profile</a>
    <?php elseif ($userRole === 'homeowner'): ?>
      <a href="<?= BASE_URL ?>/boundary/homeowner/search_available_cleaners.php">Find Cleaners</a>
      <a href="<?= BASE_URL ?>/boundary/homeowner/view_cleaner_profile.php">View Profile</a>
      <a href="<?= BASE_URL ?>/boundary/shortlist/add_to_shortlist.php">Add to Shortlist</a>
      <a href="<?= BASE_URL ?>/boundary/shortlist/view_shortlist.php">My Shortlist</a>
      <a href="<?= BASE_URL ?>/boundary/history/service_usage_history.php">History</a>
      <a href="<?= BASE_URL ?>/boundary/profile/view_user_profile.php">My Profile</a>
    <?php elseif ($userRole === 'manager'): ?>
      <a href="<?= BASE_URL ?>/boundary/category/manage_service_types.php">Manage Types</a>
      <a href="<?= BASE_URL ?>/boundary/report/manage_reports.php">Reports</a>
    <?php endif; ?>
  </nav>

  <div class="content">
<?php endif; ?>
