<?php
// public/dashboard.php
require_once __DIR__ . '/../boundary/partials/header.php';
if (!isset($_SESSION['user'], $_SESSION['role'])) {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['user']['username'];
$userId = $_SESSION['user']['id'];
?>
<h2>Dashboard</h2>
<p>Welcome, <strong><?= htmlspecialchars($username) ?></strong> (<?= htmlspecialchars($role) ?><?= $role === 'cleaner' ? ', ID: ' . $userId : '' ?>)</p>

<?php if ($role === 'admin'): ?>
    <div class="module-grid">
        <!-- User Creation Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Create User</h3>
            </div>
            <div class="module-content">
                <p>Create new user accounts for administrators, cleaners, homeowners, and managers.</p>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/admin/create_user_account.php" class="btn btn-small">Create User</a>
            </div>
        </div>

        <!-- User Management Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">User Management</h3>
            </div>
            <div class="module-content">
                <p>View, update, or suspend existing user accounts.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/admin/view_user_account.php" class="btn btn-small">View Users</a>
                    <a href="/Cleanplatform/boundary/admin/update_user_account.php" class="btn btn-small">Update User</a>
                </div>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/admin/suspend_user_account.php" class="btn btn-small">Suspend User</a>
            </div>
        </div>

        <!-- User Search Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Search Users</h3>
            </div>
            <div class="module-content">
                <p>Search for user accounts by username, email, or role.</p>
                <form action="/Cleanplatform/boundary/admin/search_user_account.php" method="get" class="module-actions">
                    <div class="form-group">
                        <input type="text" name="keyword" placeholder="Enter username or email...">
                    </div>
                    <button type="submit" class="btn btn-small">Search Users</button>
                </form>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/admin/search_user_account.php" class="btn btn-small">Advanced Search</a>
            </div>
        </div>
    </div>

<?php elseif ($role === 'cleaner'): ?>
    <div class="module-grid">
        <!-- Service Management Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Service Management</h3>
            </div>
            <div class="module-content">
                <p>Create and manage your cleaning services.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/service/manage_cleaning_services.php?tab=create" class="btn btn-small">Create Service</a>
                    <a href="/Cleanplatform/boundary/service/manage_cleaning_services.php?tab=view" class="btn btn-small">View Services</a>
                </div>
            </div>
        </div>

        <!-- Service Search Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Search Services</h3>
            </div>
            <div class="module-content">
                <p>Search through your cleaning services.</p>
                <form action="/Cleanplatform/boundary/service/manage_cleaning_services.php" method="get" class="module-actions">
                    <input type="hidden" name="tab" value="search">
                    <div class="form-group">
                        <input type="text" name="q" placeholder="Search by name, type, or description...">
                    </div>
                    <button type="submit" class="btn btn-small">Search Services</button>
                </form>
            </div>
        </div>

        <!-- Statistics Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Statistics</h3>
            </div>
            <div class="module-content">
                <p>View your profile statistics and service performance.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/history/view_service_profile_views.php" class="btn btn-small">Profile Views</a>
                    <a href="/Cleanplatform/boundary/history/view_service_shortlist_count.php" class="btn btn-small">Shortlist Count</a>
                </div>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/history/search_confirmed_matches.php" class="btn btn-small">View Matches</a>
            </div>
        </div>

        <!-- Profile Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">My Profile</h3>
            </div>
            <div class="module-content">
                <p>Manage your profile information and visibility.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/profile/view_user_profile.php" class="btn btn-small">View Profile</a>
                    <a href="/Cleanplatform/boundary/profile/update_user_profile.php" class="btn btn-small">Update Profile</a>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'homeowner'): ?>
    <div class="module-grid">
        <!-- Latest Services Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Latest Services</h3>
            </div>
            <div class="module-content">
                <p>Check out these recently added cleaning services:</p>
                <?php
                // Get latest services
                require_once __DIR__ . '/../config/Database.php';
                $db = \Config\Database::getConnection();
                $sql = 'SELECT cs.id, cs.name, cs.type, cs.price, cs.user_id, u.username AS cleaner_name
                       FROM cleaner_services cs
                       JOIN users u ON cs.user_id = u.id
                       WHERE u.role = "cleaner"
                       ORDER BY cs.created_at DESC LIMIT 5';
                $result = mysqli_query($db, $sql);
                if ($result && mysqli_num_rows($result) > 0):
                ?>
                <div class="service-list">
                    <?php while ($service = mysqli_fetch_assoc($result)): ?>
                        <div class="service-item">
                            <div class="service-details">
                                <h4><?= htmlspecialchars($service['name']) ?></h4>
                                <p class="service-type"><?= htmlspecialchars($service['type']) ?></p>
                                <p class="service-price">$<?= htmlspecialchars($service['price']) ?></p>
                                <p class="service-provider">By: <?= htmlspecialchars($service['cleaner_name']) ?></p>
                                <p class="service-id" style="background-color: #f0f8ff; padding: 4px; border-radius: 3px; font-weight: bold;">Service ID: <?= htmlspecialchars($service['id']) ?></p>
                            </div>
                            <div class="service-actions">
                                <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php?id=<?= htmlspecialchars($service['user_id']) ?>" class="btn btn-small" style="background-color: #007bff; color: white;">View Cleaner</a>
                                <form action="/Cleanplatform/boundary/shortlist/add_to_shortlist.php" method="post" style="display: inline;">
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                    <button type="submit" class="btn btn-small">Add to Shortlist</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p>No services available at the moment.</p>
                <?php endif; ?>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" class="btn btn-small">Browse All Services</a>
            </div>
        </div>

        <!-- Find Cleaners Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Find Cleaners</h3>
            </div>
            <div class="module-content">
                <p>Search for available cleaners in your area.</p>
                <form action="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" method="get" class="module-actions">
                    <div class="form-group">
                        <input type="text" name="keyword" placeholder="Enter service type or keyword...">
                    </div>
                    <button type="submit" class="btn btn-small">Find Cleaners</button>
                </form>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" class="btn btn-small">All Services</a>
            </div>
        </div>

        <!-- Shortlist Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">My Shortlist</h3>
            </div>
            <div class="module-content">
                <p>Manage your shortlisted cleaners.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/shortlist/view_shortlist.php" class="btn btn-small">View Shortlist</a>
                </div>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/shortlist/add_to_shortlist.php" class="btn btn-small">Add to Shortlist by ID</a>
            </div>
        </div>

        <!-- History Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Usage History</h3>
            </div>
            <div class="module-content">
                <p>View your service usage history.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/history/service_usage_history.php" class="btn btn-small">History</a>
                </div>
            </div>
        </div>

        <!-- Profile Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">My Profile</h3>
            </div>
            <div class="module-content">
                <p>Manage your profile information.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/profile/view_user_profile.php" class="btn btn-small">View Profile</a>
                    <a href="/Cleanplatform/boundary/profile/update_user_profile.php" class="btn btn-small">Update Profile</a>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'manager'): ?>
    <div class="module-grid">
        <!-- Category Management Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Category Management</h3>
            </div>
            <div class="module-content">
                <p>Create and manage service categories.</p>
                <div class="module-actions">
                <a href="/Cleanplatform/boundary/category/manage_service_categories.php?tab=create" class="btn btn-small">Create Category</a>
                </div>

            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/category/manage_service_categories.php" class="btn btn-small">Manage Categories</a>
            </div>
        </div>

        <!-- Search Categories Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Search Categories</h3>
            </div>
            <div class="module-content">
                <p>Search for service categories by name or description.</p>
                <form action="/Cleanplatform/boundary/category/manage_service_categories.php" method="get" class="module-actions">
                    <input type="hidden" name="tab" value="search">
                    <div class="form-group">
                        <input type="text" name="q" placeholder="Enter category name...">
                    </div>
                    <div class="module-footer">
                    <button type="submit" class="btn btn-small">Search Categories</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports Module -->
        <div class="module">
            <div class="module-header">
                <h3 class="module-title">Reports</h3>
            </div>
            <div class="module-content">
                <p>Generate and view platform reports.</p>
                <div class="module-actions">
                    <a href="/Cleanplatform/boundary/report/manage_reports.php?tab=daily" class="btn btn-small">Daily Report</a>
                    <a href="/Cleanplatform/boundary/report/manage_reports.php?tab=weekly" class="btn btn-small">Weekly Report</a>
                </div>
            </div>
            <div class="module-footer">
                <a href="/Cleanplatform/boundary/report/manage_reports.php?tab=monthly" class="btn btn-small">Monthly Report</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
.service-list {
    margin-top: 15px;
}
.service-item {
    background-color: #f9f9f9;
    border-radius: 5px;
    padding: 12px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.service-details h4 {
    margin-top: 0;
    margin-bottom: 5px;
    color: #333;
}
.service-type, .service-price, .service-provider {
    margin: 3px 0;
    font-size: 14px;
}
.service-type {
    color: #666;
}
.service-price {
    font-weight: bold;
    color: #28a745;
}
.service-provider {
    font-style: italic;
    color: #6c757d;
}
.service-actions {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
</style>
<?php require_once __DIR__ . '/../boundary/partials/footer.php'; ?>
