<?php
// boundary/history/search_confirmed_matches.php
namespace Boundary;

use Controller\SearchConfirmedMatchesController;
use Controller\ViewConfirmedMatchDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/SearchConfirmedMatchesController.php';
require_once __DIR__ . '/../../controller/ViewConfirmedMatchDetailsController.php';

// Verify user is a cleaner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Get current cleaner's ID
$cleanerId = $_SESSION['user']['id'];

// Initialize filter
$filter = [];
$title = "All Confirmed Matches";

// If search parameters are provided, use them
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['from'], $_GET['to']) && !empty($_GET['from']) && !empty($_GET['to'])) {
    $filter = ['from' => $_GET['from'], 'to' => $_GET['to']];
    $title = "Matches from " . htmlspecialchars($_GET['from']) . " to " . htmlspecialchars($_GET['to']);
}

// Get all matches for this cleaner
$controller = new SearchConfirmedMatchesController();
$results = $controller->execute($cleanerId, $filter);

// Get match details controller for additional information if needed
$detailsController = new ViewConfirmedMatchDetailsController();
?>

<h2>Confirmed Matches</h2>

<div class="card">
    <div class="card-title">Filter Matches by Date</div>
    <form method="get" class="search-form">
        <div class="form-group">
            <label for="from">From:</label>
            <input type="date" id="from" name="from" value="<?= isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="to">To:</label>
            <input type="date" id="to" name="to" value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Search</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='search_confirmed_matches.php'">Clear Filters</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title"><?= $title ?></div>
    
    <?php if (empty($results)): ?>
        <div class="notice">
            <p>No matches found for the selected criteria.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Match ID</th>
                        <th>Homeowner</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $match): ?>
                        <tr>
                            <td><?= htmlspecialchars($match['id']) ?></td>
                            <td><?= htmlspecialchars($match['homeowner_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($match['service_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($match['service_date']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(htmlspecialchars($match['status'])) ?>">
                                    <?= htmlspecialchars($match['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_match_details.php?id=<?= $match['id'] ?>" class="btn btn-small">Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    padding: 20px;
}
.card-title {
    border-bottom: 1px solid #eee;
    font-size: 18px;
    margin: -20px -20px 20px;
    padding: 15px 20px;
    background: #f8f9fa;
}
.form-group {
    margin-bottom: 15px;
    display: inline-block;
    margin-right: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-actions {
    display: inline-block;
    vertical-align: bottom;
}
.notice {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #4285f4;
    margin: 15px 0;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
}
.data-table th, 
.data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}
.data-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}
.data-table tbody tr:hover {
    background-color: #f8f9fa;
}
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}
.status-confirmed {
    background-color: #d4edda;
    color: #155724;
}
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}
.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}
.table-responsive {
    overflow-x: auto;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
