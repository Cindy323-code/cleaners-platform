<?php
// boundary/service/delete_cleaning_service.php
namespace Boundary;

use Controller\DeleteCleaningServiceController;
use Controller\ViewCleaningServicesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/DeleteCleaningServiceController.php';
require_once __DIR__ . '/../../controller/ViewCleaningServicesController.php';

// Verify user is a cleaner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Get current cleaner's ID
$cleanerId = $_SESSION['user']['id'];

// Get services list for dropdown menu
$serviceController = new ViewCleaningServicesController();
$servicesList = $serviceController->execute($cleanerId);

// Handle form submission
$message = '';
$deletedServiceId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = intval($_POST['service_id']);
    
    // Verify service ID belongs to this cleaner
    $serviceExists = false;
    foreach ($servicesList as $service) {
        if ($service['id'] == $serviceId) {
            $serviceExists = true;
            break;
        }
    }
    
    if ($serviceExists) {
        // Modify the deleteService method to include cleaner_id in the WHERE clause
        $controller = new DeleteCleaningServiceController();
        $result = $controller->execute($serviceId, $cleanerId);
        
        if ($result) {
            $message = 'Service deleted successfully!';
            $deletedServiceId = $serviceId;
            // Refresh service list after successful deletion
            $servicesList = $serviceController->execute($cleanerId);
        } else {
            $message = 'Delete failed, please try again later.';
        }
    } else {
        $message = 'Invalid service ID or you do not have permission to delete this service.';
    }
}
?>

<h2>Delete Cleaning Service</h2>

<?php if ($message): ?>
    <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-title">Select Service to Delete</div>
    
    <?php if (empty($servicesList)): ?>
        <p>You haven't created any services yet. <a href="create_cleaning_service.php">Create a service now</a></p>
    <?php else: ?>
        <form method="post" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.');">
            <div class="form-group">
                <label for="service-select">Select Service:</label>
                <select id="service-select" name="service_id" required>
                    <option value="">-- Select a Service --</option>
                    <?php foreach ($servicesList as $service): ?>
                        <option value="<?= $service['id'] ?>">
                            <?= htmlspecialchars($service['name']) ?> (<?= htmlspecialchars($service['type']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Delete Service</button>
                <a href="view_cleaning_services.php" class="btn btn-secondary">Back to Services List</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-title">Important Information</div>
    <div class="warning">
        <strong>Warning:</strong> Deleting a service is permanent and cannot be undone. 
        All data associated with this service will be permanently removed from the system.
    </div>
    <p>Consider the following before deleting:</p>
    <ul>
        <li>Customers who have this service in their shortlist will no longer see it</li>
        <li>Any pending bookings for this service will need to be handled manually</li>
        <li>Service history and statistics will be lost</li>
    </ul>
    <p>If you want to temporarily hide a service instead of deleting it, consider updating its status in the <a href="update_cleaning_service.php">Update Service</a> page.</p>
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
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.form-actions {
    margin-top: 20px;
}
.btn {
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    padding: 10px 15px;
    text-decoration: none;
}
.btn-danger {
    background: #dc3545;
}
.btn-secondary {
    background: #6c757d;
}
.message {
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
