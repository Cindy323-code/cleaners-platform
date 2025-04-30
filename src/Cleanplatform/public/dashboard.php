<?php
// public/dashboard.php
require_once __DIR__ . '/../boundary/partials/header.php';
if (!isset($_SESSION['user'], $_SESSION['role'])) {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$role     = $_SESSION['role'];
$username = $_SESSION['user']['username'];
?>
<h2>Dashboard</h2>
<p>Welcome, <strong><?= htmlspecialchars($username) ?></strong> (<?= htmlspecialchars($role) ?>)</p>

<?php if ($role === 'admin'): ?>
    <ul>
      <li><a href="/Cleanplatform/boundary/admin/create_user_account.php">Create User</a></li>
      <li><a href="/Cleanplatform/boundary/admin/view_user_account.php">View User</a></li>
      <li><a href="/Cleanplatform/boundary/admin/update_user_account.php">Update User</a></li>
      <li><a href="/Cleanplatform/boundary/admin/suspend_user_account.php">Suspend User</a></li>
      <li><a href="/Cleanplatform/boundary/admin/search_user_account.php">Search Users</a></li>
    </ul>

  <?php elseif ($role === 'cleaner'): ?>
    <ul>
      <!-- Service Management -->
      <li><a href="/Cleanplatform/boundary/service/create_cleaning_service.php">Create Service</a></li>
      <li><a href="/Cleanplatform/boundary/service/view_cleaning_services.php">My Services</a></li>
      <li><a href="/Cleanplatform/boundary/service/update_cleaning_service.php">Update Service</a></li>
      <li><a href="/Cleanplatform/boundary/service/delete_cleaning_service.php">Delete Service</a></li>
      <li><a href="/Cleanplatform/boundary/service/search_cleaning_services.php">Search Services</a></li>

      <hr>
      <!-- Stats & History -->
      <li><a href="/Cleanplatform/boundary/history/view_service_profile_views.php">Profile View Count</a></li>
      <li><a href="/Cleanplatform/boundary/history/view_service_shortlist_count.php">Shortlist Count</a></li>
      <li><a href="/Cleanplatform/boundary/history/search_confirmed_matches.php">Search Confirmed Matches</a></li>
      <li><a href="/Cleanplatform/boundary/history/view_confirmed_match_details.php">View Match Details</a></li>

      <hr>
      <!-- User Profile -->
      <li><strong>My Profile</strong></li>
      <li><a href="/Cleanplatform/boundary/profile/create_user_profile.php">Create My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/view_user_profile.php">View My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/update_user_profile.php">Update My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/deactivate_user_profile.php">Deactivate My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/search_user_profile.php">Search Other Profiles</a></li>
    </ul>

  <?php elseif ($role === 'homeowner'): ?>
    <ul>
      <!-- Find & Shortlist -->
      <li><a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php">Find Cleaners</a></li>
      <li><a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php">View Cleaner Profile</a></li>
      <hr>
      <li><a href="/Cleanplatform/boundary/shortlist/add_to_shortlist.php">Add to Shortlist</a></li>
      <li><a href="/Cleanplatform/boundary/shortlist/view_shortlist.php">My Shortlist</a></li>
      <li><a href="/Cleanplatform/boundary/shortlist/search_shortlist.php">Search Shortlist</a></li>

      <hr>
      <!-- Usage History -->
      <li><a href="/Cleanplatform/boundary/history/search_service_usage_history.php">Search Usage History</a></li>
      <li><a href="/Cleanplatform/boundary/history/view_service_usage_details.php">View Usage Details</a></li>

      <hr>
      <!-- User Profile -->
      <li><strong>My Profile</strong></li>
      <li><a href="/Cleanplatform/boundary/profile/create_user_profile.php">Create My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/view_user_profile.php">View My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/update_user_profile.php">Update My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/deactivate_user_profile.php">Deactivate My Profile</a></li>
      <li><a href="/Cleanplatform/boundary/profile/search_user_profile.php">Search Other Profiles</a></li>
    </ul>

  <?php elseif ($role === 'manager'): ?>
    <ul>
      <!-- Category Management -->
      <li><a href="/Cleanplatform/boundary/category/create_service_category.php">Create Category</a></li>
      <li><a href="/Cleanplatform/boundary/category/view_service_categories.php">View Categories</a></li>
      <li><a href="/Cleanplatform/boundary/category/update_service_category.php">Update Category</a></li>
      <li><a href="/Cleanplatform/boundary/category/delete_service_category.php">Delete Category</a></li>
      <li><a href="/Cleanplatform/boundary/category/search_service_category.php">Search Categories</a></li>

      <hr>
      <!-- Reports -->
      <li><a href="/Cleanplatform/boundary/report/daily.php">Daily Report</a></li>
      <li><a href="/Cleanplatform/boundary/report/weekly.php">Weekly Report</a></li>
      <li><a href="/Cleanplatform/boundary/report/monthly.php">Monthly Report</a></li>
    </ul>
  <?php endif; ?>
<?php require_once __DIR__ . '/../boundary/partials/footer.php'; ?>
