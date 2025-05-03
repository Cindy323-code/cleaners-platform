<?php
// boundary/history/view_service_usage_details.php
namespace Boundary;

use Controller\ViewServiceUsageDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewServiceUsageDetailsController.php';

// Check if user is logged in as homeowner
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$matchId = intval($_GET['id'] ?? 0);
$details = $matchId ? (new ViewServiceUsageDetailsController())->execute($matchId) : null;
?>

<h2>Service Usage Details</h2>

<div class="card">
    <div class="card-title">Usage Information</div>

    <?php if ($details): ?>
        <div class="details-list">
            <?php foreach ($details as $key => $value): ?>
                <div class="detail-item">
                    <div class="detail-label"><?= htmlspecialchars(ucfirst($key)) ?>:</div>
                    <div class="detail-value"><?= htmlspecialchars($value) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>No details available for this service usage.</p>
        </div>
    <?php endif; ?>

    <div class="button-group">
        <a href="/Cleanplatform/boundary/history/service_usage_history.php" class="btn">Back to History</a>
        <a href="/Cleanplatform/public/dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
</div>

<style>
.details-list {
    margin: 20px 0;
}
.detail-item {
    display: flex;
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}
.detail-label {
    font-weight: bold;
    width: 150px;
    color: #555;
}
.detail-value {
    flex: 1;
}
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>