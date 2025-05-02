<?php
// ── partials/header.php ─────────────────────────────────────────
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
// Only redirect and show navigation on non-login pages
$isLoginPage = basename($_SERVER['SCRIPT_NAME']) === 'login.php';
if (!isset($_SESSION['user']) && !$isLoginPage) {
  header('Location: /Cleanplatform/boundary/auth/login.php');
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
  <link rel="stylesheet" href="/Cleanplatform/public/css/style.css">
</head>
<body>
<div class="container">
  <header class="header">
    <h2>Cleaning Platform Management System</h2>
    <div class="user-info">
      <?php if ($userRole !== 'guest'): ?>
        <span>
          Welcome, <strong><?= htmlspecialchars($username) ?></strong> (<?= htmlspecialchars($userRole) ?>)
          | <a href="/Cleanplatform/public/logout.php">Logout</a>
        </span>
      <?php else: ?>
        <span><a href="/Cleanplatform/boundary/auth/login.php">Login</a></span>
      <?php endif; ?>
    </div>
  </header>
  
  <nav class="nav">
    <a href="/Cleanplatform/public/dashboard.php">Dashboard</a>
    <?php if ($userRole === 'admin'): ?>
      <a href="/Cleanplatform/boundary/admin/create_user_account.php">Create User</a>
      <a href="/Cleanplatform/boundary/admin/view_user_account.php">View Users</a>
      <a href="/Cleanplatform/boundary/admin/update_user_account.php">Update User</a>
      <a href="/Cleanplatform/boundary/admin/suspend_user_account.php">Suspend User</a>
      <a href="/Cleanplatform/boundary/admin/search_user_account.php">Search Users</a>
    <?php elseif ($userRole === 'cleaner'): ?>
      <a href="/Cleanplatform/boundary/service/create_cleaning_service.php">Create Service</a>
      <a href="/Cleanplatform/boundary/service/view_cleaning_services.php">My Services</a>
      <a href="/Cleanplatform/boundary/service/update_cleaning_service.php">Update Service</a>
      <a href="/Cleanplatform/boundary/service/delete_cleaning_service.php">Delete Service</a>
      <a href="/Cleanplatform/boundary/service/search_cleaning_services.php">Search Services</a>
      <a href="/Cleanplatform/boundary/history/view_service_profile_views.php">Profile Views</a>
      <a href="/Cleanplatform/boundary/history/view_service_shortlist_count.php">Shortlist Count</a>
      <a href="/Cleanplatform/boundary/history/search_confirmed_matches.php">Matches</a>
      <a href="/Cleanplatform/boundary/profile/view_user_profile.php">My Profile</a>
    <?php elseif ($userRole === 'homeowner'): ?>
      <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php">Find Cleaners</a>
      <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php">View Profile</a>
      <a href="/Cleanplatform/boundary/shortlist/add_to_shortlist.php">Add to Shortlist</a>
      <a href="/Cleanplatform/boundary/shortlist/view_shortlist.php">My Shortlist</a>
      <a href="/Cleanplatform/boundary/history/service_usage_history.php">History</a>
      <a href="/Cleanplatform/boundary/profile/view_user_profile.php">My Profile</a>
    <?php elseif ($userRole === 'manager'): ?>
      <a href="/Cleanplatform/boundary/category/create_service_category.php">Create Category</a>
      <a href="/Cleanplatform/boundary/category/view_service_categories.php">View Categories</a>
      <a href="/Cleanplatform/boundary/category/update_service_category.php">Update Category</a>
      <a href="/Cleanplatform/boundary/category/delete_service_category.php">Delete Category</a>
      <a href="/Cleanplatform/boundary/category/search_service_category.php">Search Categories</a>
      <a href="/Cleanplatform/boundary/report/daily.php">Daily Report</a>
      <a href="/Cleanplatform/boundary/report/weekly.php">Weekly Report</a>
      <a href="/Cleanplatform/boundary/report/monthly.php">Monthly Report</a>
    <?php endif; ?>
  </nav>
  
  <div class="content">
<?php endif; ?>
