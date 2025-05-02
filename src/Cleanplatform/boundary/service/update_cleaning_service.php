<?php
// boundary/service/update_cleaning_service.php
namespace Boundary;

use Controller\UpdateCleaningServiceController;
use Controller\ViewCleaningServicesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/UpdateCleaningServiceController.php';
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
$updatedService = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = intval($_POST['service_id']);
    
    // Verify service ID belongs to this cleaner
    $serviceExists = false;
    foreach ($servicesList as $service) {
        if ($service['id'] == $serviceId) {
            $serviceExists = true;
            $updatedService = $service; // Save current service info to display in form
            break;
        }
    }
    
    if ($serviceExists) {
        // Prepare update fields, only update non-empty fields
        $fields = [];
        if (isset($_POST['name']) && $_POST['name'] !== '') $fields['name'] = trim($_POST['name']);
        if (isset($_POST['type']) && $_POST['type'] !== '') $fields['type'] = trim($_POST['type']);
        if (isset($_POST['price']) && $_POST['price'] !== '') $fields['price'] = floatval($_POST['price']);
        if (isset($_POST['description']) && $_POST['description'] !== '') $fields['description'] = trim($_POST['description']);
        
        if (!empty($fields)) {
            $controller = new UpdateCleaningServiceController();
            $result = $controller->execute($serviceId, $fields);
            
            if ($result) {
                $message = 'Service updated successfully!';
                // Refresh service list after successful update
                $servicesList = $serviceController->execute($cleanerId);
                
                // Update local service info to display in form
                foreach ($servicesList as $service) {
                    if ($service['id'] == $serviceId) {
                        $updatedService = $service;
                        break;
                    }
                }
            } else {
                $message = 'Update failed, please try again later.';
            }
        } else {
            $message = 'No update fields provided.';
        }
    } else {
        $message = 'Invalid service ID or you do not have permission to update this service.';
    }
} else if (isset($_GET['id'])) {
    // If accessed via URL parameter, preload service info
    $serviceId = intval($_GET['id']);
    foreach ($servicesList as $service) {
        if ($service['id'] == $serviceId) {
            $updatedService = $service;
            break;
        }
    }
}
?>

<h2>Update Cleaning Service</h2>

<?php if ($message): ?>
    <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-title">Select Service to Update</div>
    
    <?php if (empty($servicesList)): ?>
        <p>You haven't created any services yet. <a href="create_cleaning_service.php">Create a service now</a></p>
    <?php else: ?>
        <form method="get" class="service-selector">
            <div class="form-group">
                <label for="service-select">Select Service:</label>
                <select id="service-select" name="id" onchange="this.form.submit()">
                    <option value="">-- Select a Service --</option>
                    <?php foreach ($servicesList as $service): ?>
                        <option value="<?= $service['id'] ?>" <?= $updatedService && $updatedService['id'] == $service['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['name']) ?> (<?= htmlspecialchars($service['type']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php if ($updatedService): ?>
<div class="card">
    <div class="card-title">Update Service Details</div>
    
    <form method="post">
        <input type="hidden" name="service_id" value="<?= $updatedService['id'] ?>">
        
        <div class="form-group">
            <label for="name">Service Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($updatedService['name']) ?>" placeholder="e.g., Deep Cleaning">
        </div>
        
        <div class="form-group">
            <label for="type">Service Type:</label>
            <input type="text" id="type" name="type" value="<?= htmlspecialchars($updatedService['type']) ?>" placeholder="e.g., Home Cleaning">
        </div>
        
        <div class="form-group">
            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($updatedService['price']) ?>" step="0.01" min="0">
        </div>
        
        <div class="form-group">
            <label for="description">Service Description:</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($updatedService['description']) ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Update Service</button>
            <a href="view_cleaning_services.php" class="btn btn-secondary">Back to Services List</a>
        </div>
    </form>
</div>
<?php elseif (!empty($servicesList)): ?>
<div class="card">
    <div class="card-title">Please select a service from above to update</div>
</div>
<?php endif; ?>

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
.form-group input, 
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.form-actions {
    margin-top: 20px;
}
.btn {
    background: #4285f4;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    padding: 10px 15px;
    text-decoration: none;
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
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>