<?php
// boundary/shortlist/view_shortlist.php
namespace Boundary;

use Controller\ViewShortlistController;
use Controller\SearchShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/ViewShortlistController.php';
require_once __DIR__ . '/../../controller/SearchShortlistController.php';

// Check if user is logged in as homeowner
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Handle search
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
if (!empty($keyword)) {
    $list = (new SearchShortlistController())->execute($_SESSION['user']['id'], $keyword);
} else {
    $list = (new ViewShortlistController())->execute($_SESSION['user']['id']);
}

// Handle messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$success = isset($_GET['success']) ? (bool)$_GET['success'] : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shortlist - Cleaning Platform</title>
    <link rel="stylesheet" href="/Cleanplatform/public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Your Shortlist</h2>
            <div class="user-info">
                <p>Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?></p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="shortlist-actions">
            <form action="view_shortlist.php" method="get" class="search-form">
                <div class="search-container">
                    <input type="text" name="keyword" placeholder="Search by service name..." value="<?= htmlspecialchars($keyword) ?>">
                    <button type="submit" class="btn btn-small">Search</button>
                    <?php if (!empty($keyword)): ?>
                        <a href="view_shortlist.php" class="btn btn-small btn-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
            <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" class="btn btn-small">Find More Cleaners</a>
        </div>

        <?php if (empty($list)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>Your shortlist is empty</h3>
                <p>You haven't added any cleaning services to your shortlist yet.</p>
                <a href="/Cleanplatform/boundary/homeowner/search_available_cleaners.php" class="btn">Browse Available Cleaners</a>
            </div>
        <?php else: ?>
            <div class="shortlist-grid">
                <?php foreach ($list as $item): ?>
                    <div class="shortlist-card">
                        <div class="shortlist-header">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <span class="service-type"><?= htmlspecialchars($item['type']) ?></span>
                        </div>
                        <div class="shortlist-content">
                            <p class="service-price">$<?= htmlspecialchars($item['price']) ?></p>
                            <p class="service-id">Service ID: <?= htmlspecialchars($item['service_id']) ?></p>
                            <p class="cleaner-id">Cleaner ID: <?= htmlspecialchars($item['cleaner_id']) ?></p>
                            <?php
                            // 查询service description using service_id
                            $serviceDescOutput = '';
                            if (!empty($item['service_id'])) {
                                $db = \Config\Database::getConnection();
                                $stmt_desc = mysqli_prepare($db, 'SELECT description FROM cleaner_services WHERE id = ?');
                                mysqli_stmt_bind_param($stmt_desc, 'i', $item['service_id']);
                                mysqli_stmt_execute($stmt_desc);
                                mysqli_stmt_bind_result($stmt_desc, $desc_val);
                                if (mysqli_stmt_fetch($stmt_desc)) {
                                    $serviceDescOutput = $desc_val;
                                }
                                mysqli_stmt_close($stmt_desc);
                            }
                            if (!empty($serviceDescOutput)) {
                                echo '<div class="service-description">Description: ' . htmlspecialchars($serviceDescOutput) . '</div>';
                            }
                            ?>
                            <div class="shortlist-actions">
                                <form action="remove_from_shortlist.php" method="post" onsubmit="return confirm('Are you sure you want to remove this service from your shortlist?');">
                                    <input type="hidden" name="shortlist_id" value="<?= $item['shortlist_id'] ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Remove</button>
                                </form>
                                <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php?id=<?= htmlspecialchars($item['cleaner_id']) ?>" class="btn btn-small">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="page-footer">
            <a href="/Cleanplatform/public/dashboard.php" class="btn">&laquo; Back to Dashboard</a>
        </div>
    </div>

    <style>
        .shortlist-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container {
            display: flex;
            gap: 10px;
        }

        .search-container input {
            width: 300px;
            padding: 8px 12px;
        }

        .shortlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .shortlist-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .shortlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .shortlist-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .shortlist-header h3 {
            margin: 0 0 5px 0;
            color: var(--primary-color);
        }

        .service-type {
            display: inline-block;
            background: rgba(66, 133, 244, 0.1);
            color: var(--primary-color);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .shortlist-content {
            padding: 15px;
        }

        .service-price {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #34a853;
        }

        .shortlist-actions {
            display: flex;
            justify-content: space-between;
        }

        .btn-danger {
            background-color: #ea4335;
        }

        .btn-danger:hover {
            background-color: #d73125;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 30px 0;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #555;
        }

        .empty-state p {
            margin-bottom: 20px;
            color: #777;
        }

        .page-footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</body>
</html>
