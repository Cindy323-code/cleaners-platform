<?php
// boundary/history/view_confirmed_match_details.php
namespace Boundary;

use Controller\ViewConfirmedMatchDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewConfirmedMatchDetailsController.php';

// Check if user is logged in as cleaner
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$matchId = intval($_GET['id'] ?? 0);
$details = $matchId ? (new ViewConfirmedMatchDetailsController())->execute($matchId) : null;
?>

<h2>Match Details</h2>

<div class="card">
    <div class="card-title">Service Match Information</div>

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
            <p>No details available for this match.</p>
        </div>
    <?php endif; ?>

    <div class="button-group">
        <a href="/Cleanplatform/boundary/history/search_confirmed_matches.php" class="btn">Back to Matches</a>
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