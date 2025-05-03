<?php
// boundary/history/view_service_profile_views.php
namespace Boundary;

use Controller\ViewServiceProfileViewsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewServiceProfileViewsController.php';

// Check if user is logged in as cleaner
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$count = (new ViewServiceProfileViewsController())->execute($_SESSION['user']['id']);
?>

<h2>Profile View Statistics</h2>

<div class="card">
    <div class="card-title">Profile View Count</div>
    <div class="stat-display">
        <div class="stat-value"><?= htmlspecialchars($count) ?></div>
        <div class="stat-label">Total Views</div>
    </div>
    <p class="stat-description">This is the total number of times your profile has been viewed by potential clients.</p>
    <div class="button-group">
        <a href="/Cleanplatform/public/dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</div>

<style>
.stat-display {
    text-align: center;
    padding: 30px 0;
}
.stat-value {
    font-size: 48px;
    font-weight: bold;
    color: var(--primary-color);
}
.stat-label {
    font-size: 18px;
    color: #666;
    margin-top: 10px;
}
.stat-description {
    text-align: center;
    margin: 20px 0;
    color: #666;
}
.button-group {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
