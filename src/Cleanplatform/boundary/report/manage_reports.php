<?php
// boundary/report/manage_reports.php
namespace Boundary;

use Controller\GenerateDailyReportController;
use Controller\GenerateWeeklyReportController;
use Controller\GenerateMonthlyReportController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/GenerateDailyReportController.php';
require_once __DIR__ . '/../../controller/GenerateWeeklyReportController.php';
require_once __DIR__ . '/../../controller/GenerateMonthlyReportController.php';

// Check if user is logged in as manager
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Set default dates for form values
$today = date('Y-m-d');
$firstDayOfMonth = date('Y-m-01');
$lastDayOfMonth = date('Y-m-t');
$currentYear = date('Y');
$currentMonth = date('n');
$oneWeekAgo = date('Y-m-d', strtotime('-7 days'));

// Initialize variables
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'daily';
$reportData = [];
$reportTitle = '';
$reportPeriod = '';

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Daily Report
    if ($activeTab === 'daily') {
        // Use provided date or default to today
        $date = isset($_GET['date']) ? $_GET['date'] : $today;
        $reportData = (new GenerateDailyReportController())->execute($date);
        $reportTitle = 'Daily Report';
        $reportPeriod = 'for ' . date('F j, Y', strtotime($date));
    }

    // Weekly Report
    else if ($activeTab === 'weekly') {
        // Use provided dates or default to last 7 days
        $start = isset($_GET['start']) ? $_GET['start'] : $oneWeekAgo;
        $end = isset($_GET['end']) ? $_GET['end'] : $today;
        $reportData = (new GenerateWeeklyReportController())->execute($start, $end);
        $reportTitle = 'Weekly Report';
        $reportPeriod = 'from ' . date('F j, Y', strtotime($start)) . ' to ' . date('F j, Y', strtotime($end));
    }

    // Monthly Report
    else if ($activeTab === 'monthly') {
        // Use provided year/month or default to current month
        $year = isset($_GET['year']) ? intval($_GET['year']) : $currentYear;
        $month = isset($_GET['month']) ? intval($_GET['month']) : $currentMonth;
        $reportData = (new GenerateMonthlyReportController())->execute($year, $month);
        $reportTitle = 'Monthly Report';

        // Get month name
        $dateObj = \DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');

        $reportPeriod = 'for ' . $monthName . ' ' . $year;
    }
}
?>

<h2>Platform Reports</h2>

<!-- Tab Navigation -->
<div class="tab-navigation">
    <a href="?tab=daily" class="tab-link <?= $activeTab === 'daily' ? 'active' : '' ?>">Daily Report</a>
    <a href="?tab=weekly" class="tab-link <?= $activeTab === 'weekly' ? 'active' : '' ?>">Weekly Report</a>
    <a href="?tab=monthly" class="tab-link <?= $activeTab === 'monthly' ? 'active' : '' ?>">Monthly Report</a>
</div>

<div class="info-message">
    <i class="fa fa-info-circle"></i> Default reports are automatically loaded for each tab. You can customize the report parameters using the form below.
</div>

<!-- Daily Report Tab -->
<?php if ($activeTab === 'daily'): ?>
    <div class="card">
        <div class="card-title">Daily Report Options</div>
        <div class="card-subtitle">Today's report is shown by default</div>

        <form method="get" action="manage_reports.php" class="report-form">
            <input type="hidden" name="tab" value="daily">

            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : $today ?>" required>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Generate Report</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Weekly Report Tab -->
<?php if ($activeTab === 'weekly'): ?>
    <div class="card">
        <div class="card-title">Weekly Report Options</div>
        <div class="card-subtitle">Last 7 days report is shown by default</div>

        <form method="get" action="manage_reports.php" class="report-form">
            <input type="hidden" name="tab" value="weekly">

            <div class="form-row">
                <div class="form-group">
                    <label for="start">Start Date:</label>
                    <input type="date" id="start" name="start" value="<?= isset($_GET['start']) ? htmlspecialchars($_GET['start']) : $oneWeekAgo ?>" required>
                </div>

                <div class="form-group">
                    <label for="end">End Date:</label>
                    <input type="date" id="end" name="end" value="<?= isset($_GET['end']) ? htmlspecialchars($_GET['end']) : $today ?>" required>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Generate Report</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Monthly Report Tab -->
<?php if ($activeTab === 'monthly'): ?>
    <div class="card">
        <div class="card-title">Monthly Report Options</div>
        <div class="card-subtitle">Current month's report is shown by default</div>

        <form method="get" action="manage_reports.php" class="report-form">
            <input type="hidden" name="tab" value="monthly">

            <div class="form-row">
                <div class="form-group">
                    <label for="year">Year:</label>
                    <select id="year" name="year" required>
                        <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= (isset($_GET['year']) && intval($_GET['year']) === $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="month">Month:</label>
                    <select id="month" name="month" required>
                        <?php
                        $months = [
                            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                        ];

                        foreach ($months as $num => $name):
                        ?>
                            <option value="<?= $num ?>" <?= (isset($_GET['month']) && intval($_GET['month']) === $num) ? 'selected' : ($num === $currentMonth && !isset($_GET['month']) ? 'selected' : '') ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Generate Report</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Report Results -->
<?php if (!empty($reportData)): ?>
    <div class="card">
        <div class="card-title"><?= htmlspecialchars($reportTitle) ?> <?= htmlspecialchars($reportPeriod) ?></div>

        <div class="report-summary">
            <div class="summary-header">
                <h3>Summary</h3>
                <span class="report-date"><?= htmlspecialchars($reportPeriod) ?></span>
            </div>

            <div class="metrics-grid">
                <?php foreach ($reportData as $key => $value): ?>
                    <div class="metric-card">
                        <div class="metric-value"><?= htmlspecialchars($value) ?></div>
                        <div class="metric-label"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="report-details">
            <h3>Detailed Metrics</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $key => $value): ?>
                            <tr>
                                <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="report-actions">
            <button onclick="window.print()" class="btn">Print Report</button>
        </div>
    </div>
<?php endif; ?>

<style>
/* Tab Navigation */
.tab-navigation {
    display: flex;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

/* Info Message */
.info-message {
    background-color: #e7f3fe;
    border-left: 4px solid #2196F3;
    margin-bottom: 20px;
    padding: 12px;
    color: #0c5460;
    border-radius: 4px;
}

.tab-link {
    padding: 10px 15px;
    margin-right: 5px;
    text-decoration: none;
    color: #555;
    border: 1px solid transparent;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
    background-color: #f8f9fa;
}

.tab-link:hover {
    background-color: #e9ecef;
}

.tab-link.active {
    color: var(--primary-color);
    background-color: #fff;
    border-color: #ddd;
    border-bottom-color: #fff;
    margin-bottom: -1px;
    font-weight: bold;
}

/* Card Subtitle */
.card-subtitle {
    color: #6c757d;
    font-size: 14px;
    margin-top: -10px;
    margin-bottom: 15px;
}

/* Report Form */
.report-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px 0;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
    min-width: 200px;
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Report Results */
.report-summary {
    margin: 20px 0;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.summary-header h3 {
    margin: 0;
    color: #333;
}

.report-date {
    color: #666;
    font-style: italic;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.metric-card {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.metric-value {
    font-size: 32px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.metric-label {
    color: #666;
    font-size: 14px;
}

.report-details {
    margin: 20px 0;
}

.report-details h3 {
    margin-bottom: 15px;
    color: #333;
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.report-actions {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

/* Print Styles */
@media print {
    .tab-navigation, .report-form, .report-actions, nav, header {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
        border: none !important;
    }

    .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    body {
        font-size: 12pt;
    }

    h2, h3 {
        page-break-after: avoid;
    }

    table {
        page-break-inside: avoid;
    }
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
