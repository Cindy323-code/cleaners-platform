<?php
// boundary/shortlist/add_to_shortlist.php
namespace Boundary;

use Controller\AddCleanerToShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/AddCleanerToShortlistController.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'homeowner') {
        $message = 'You must be logged in as a homeowner to add services to shortlist';
    } else {
        $homeId = $_SESSION['user']['id'];
        $serviceId = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
        
        if ($serviceId) {
            // 检查服务是否已经在收藏夹中
            $db = \Config\Database::getConnection();
            $checkSql = "SELECT * FROM shortlists WHERE homeowner_id = ? AND service_id = ?";
            $checkStmt = mysqli_prepare($db, $checkSql);
            mysqli_stmt_bind_param($checkStmt, 'ii', $homeId, $serviceId);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);
            
            if (mysqli_num_rows($checkResult) > 0) {
                $message = 'This service is already in your shortlist';
            } else {
                // 添加服务到收藏夹
                $result = (new AddCleanerToShortlistController())->execute($homeId, $serviceId);
                if ($result) {
                    $message = 'Service added to your shortlist successfully';
                    $success = true;
                } else {
                    $message = 'Failed to add service to shortlist';
                }
            }
            
            mysqli_stmt_close($checkStmt);
        } else {
            $message = 'Invalid service ID';
        }
    }
}

// 获取服务信息（如果有提供service_id）
$serviceInfo = null;
if (isset($_GET['service_id']) && intval($_GET['service_id']) > 0) {
    $serviceId = intval($_GET['service_id']);
    $db = \Config\Database::getConnection();
    $sql = "SELECT cs.id, cs.name, cs.type, cs.price, c.username AS cleaner_name, c.id AS cleaner_id 
            FROM cleaner_services cs
            JOIN cleaners c ON cs.cleaner_id = c.id
            WHERE cs.id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $serviceId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $serviceInfo = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
}
?>

<div class="container">
    <h2>Add to Shortlist</h2>
    
    <?php if ($message): ?>
        <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($serviceInfo): ?>
        <div class="service-preview">
            <h3><?= htmlspecialchars($serviceInfo['name']) ?></h3>
            <div class="service-details">
                <p><strong>Type:</strong> <?= htmlspecialchars($serviceInfo['type']) ?></p>
                <p><strong>Price:</strong> $<?= htmlspecialchars($serviceInfo['price']) ?></p>
                <p><strong>Provider:</strong> <?= htmlspecialchars($serviceInfo['cleaner_name']) ?></p>
                <p><strong>Service ID:</strong> <?= htmlspecialchars($serviceInfo['id']) ?></p>
            </div>
            
            <form method="post" action="add_to_shortlist.php">
                <input type="hidden" name="service_id" value="<?= $serviceInfo['id'] ?>">
                <button type="submit" class="btn">Add to Shortlist</button>
                <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php?id=<?= $serviceInfo['cleaner_id'] ?>" class="btn">View Cleaner Profile</a>
            </form>
        </div>
    <?php else: ?>
        <form method="post" action="add_to_shortlist.php" class="shortlist-form">
            <div class="form-group">
                <label for="service_id">Service ID:</label>
                <input type="number" id="service_id" name="service_id" placeholder="Enter Service ID" value="<?= isset($_POST['service_id']) ? htmlspecialchars($_POST['service_id']) : '' ?>" required>
            </div>
            <button type="submit" class="btn">Add to Shortlist</button>
            <a href="/Cleanplatform/public/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    <?php endif; ?>
</div>

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.alert {
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.service-preview {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.service-details {
    margin: 15px 0;
}

.service-details p {
    margin: 5px 0;
}

.shortlist-form {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.btn {
    display: inline-block;
    background-color: #4285f4;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    margin-right: 10px;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn:hover {
    opacity: 0.9;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>