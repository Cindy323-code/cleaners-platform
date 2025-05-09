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
            $checkSql = "SELECT * FROM shortlists WHERE user_id = ? AND service_id = ?";
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
                    // 检查服务ID是否存在
                    $checkServiceSql = "SELECT id FROM cleaner_services WHERE id = ?";
                    $checkServiceStmt = mysqli_prepare($db, $checkServiceSql);
                    mysqli_stmt_bind_param($checkServiceStmt, 'i', $serviceId);
                    mysqli_stmt_execute($checkServiceStmt);
                    mysqli_stmt_store_result($checkServiceStmt);
                    $serviceExists = mysqli_stmt_num_rows($checkServiceStmt) > 0;
                    mysqli_stmt_close($checkServiceStmt);

                    if (!$serviceExists) {
                        $message = 'Service ID ' . $serviceId . ' does not exist. Please check the ID and try again.';
                    } else {
                        $message = 'Failed to add service to shortlist due to an unexpected error.';
                    }
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
    $sql = "SELECT cs.id, cs.name, cs.type, cs.price, u.username AS cleaner_name, u.id AS cleaner_id
            FROM cleaner_services cs
            JOIN users u ON cs.user_id = u.id
            WHERE cs.id = ? AND u.role = 'cleaner'";
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

<h2>Add to Shortlist</h2>

<?php if ($message): ?>
    <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if ($serviceInfo): ?>
    <div class="card">
        <div class="card-title"><?= htmlspecialchars($serviceInfo['name']) ?></div>
        <div class="service-details">
            <p><strong>Type:</strong> <?= htmlspecialchars($serviceInfo['type']) ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($serviceInfo['price']) ?></p>
            <p><strong>Provider:</strong> <?= htmlspecialchars($serviceInfo['cleaner_name']) ?></p>
            <p><strong>Service ID:</strong> <?= htmlspecialchars($serviceInfo['id']) ?></p>
        </div>

        <form method="post" action="add_to_shortlist.php">
            <input type="hidden" name="service_id" value="<?= $serviceInfo['id'] ?>">
            <div class="button-group">
                <button type="submit" class="btn">Add to Shortlist</button>
                <a href="/Cleanplatform/boundary/homeowner/view_cleaner_profile.php?id=<?= $serviceInfo['cleaner_id'] ?>" class="btn">View Cleaner Profile</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-title">Add Service to Shortlist</div>
        <form method="post" action="add_to_shortlist.php">
            <div class="form-group">
                <label for="service_id">Service ID:</label>
                <input type="number" id="service_id" name="service_id" placeholder="Enter Service ID" value="<?= isset($_POST['service_id']) ? htmlspecialchars($_POST['service_id']) : '' ?>" required>
            </div>
            <div class="button-group">
                <button type="submit" class="btn">Add to Shortlist</button>
                <a href="/Cleanplatform/public/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<style>
.service-details {
    margin: 15px 0;
}
.service-details p {
    margin: 10px 0;
}
.button-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>