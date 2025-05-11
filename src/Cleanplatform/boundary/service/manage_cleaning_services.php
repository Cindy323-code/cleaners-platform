<?php
// boundary/service/manage_cleaning_services.php
namespace Boundary;

use Controller\ViewCleaningServicesController;
use Controller\CreateCleaningServiceController;
use Controller\UpdateCleaningServiceController;
use Controller\DeleteCleaningServiceController;
use Controller\SearchCleaningServicesController;
use Controller\ViewServiceCategoriesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/ViewCleaningServicesController.php';
require_once __DIR__ . '/../../controller/CreateCleaningServiceController.php';
require_once __DIR__ . '/../../controller/UpdateCleaningServiceController.php';
require_once __DIR__ . '/../../controller/DeleteCleaningServiceController.php';
require_once __DIR__ . '/../../controller/SearchCleaningServicesController.php';
require_once __DIR__ . '/../../controller/ViewServiceCategoriesController.php';
require_once __DIR__ . '/../../Entity/PlatformManager.php';

// Check if user is logged in as cleaner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Get current cleaner's ID
$cleanerId = $_SESSION['user']['id'];

// Initialize variables
$message = '';
$messageType = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
$searchResults = [];
$serviceToEdit = null;
$services = [];

// Get services list for dropdown menu and view tab
$serviceController = new ViewCleaningServicesController();
$servicesList = $serviceController->execute($cleanerId);

// Get all service categories created by managers
$categoryController = new ViewServiceCategoriesController();
$categoriesList = $categoryController->execute();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create service
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $data = [
            'user_id'     => $cleanerId,
            'name'        => trim($_POST['name']),
            'type'        => trim($_POST['type']),
            'price'       => floatval($_POST['price']),
            'description' => trim($_POST['description'])
        ];

        if (empty($data['name']) || empty($data['type']) || $data['price'] <= 0) {
            $message = 'Please fill in all required fields with valid values';
            $messageType = 'error';
            $activeTab = 'create';
        } else {
            $result = (new CreateCleaningServiceController())->execute($data);
            $message = $result ? 'Service created successfully' : 'Failed to create service';
            $messageType = $result ? 'success' : 'error';
            $activeTab = $result ? 'view' : 'create';

            // Refresh service list after successful creation
            if ($result) {
                $servicesList = $serviceController->execute($cleanerId);
            }
        }
    }

    // Update service
    else if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $serviceId = intval($_POST['service_id']);
        $fields = [];

        if (!empty($_POST['name'])) {
            $fields['name'] = trim($_POST['name']);
        }

        if (!empty($_POST['type'])) {
            $fields['type'] = trim($_POST['type']);
        }

        if (isset($_POST['price']) && $_POST['price'] !== '') {
            $fields['price'] = floatval($_POST['price']);
        }

        if (isset($_POST['description'])) {
            $fields['description'] = trim($_POST['description']);
        }

        // Verify service ID belongs to this cleaner
        $serviceExists = false;
        foreach ($servicesList as $service) {
            if ($service['id'] == $serviceId) {
                $serviceExists = true;
                $serviceToEdit = $service;
                break;
            }
        }

        if (!$serviceExists) {
            $message = 'Invalid service ID or you do not have permission to update this service';
            $messageType = 'error';
            $activeTab = 'update';
        } else if (empty($fields)) {
            $message = 'No changes were made';
            $messageType = 'warning';
            $activeTab = 'update';
        } else {
            $result = (new UpdateCleaningServiceController())->execute($serviceId, $fields);
            $message = $result ? 'Service updated successfully' : 'Failed to update service';
            $messageType = $result ? 'success' : 'error';
            
            // Set the active tab based on where the user came from
            if (isset($_GET['from']) && $_GET['from'] === 'search' && isset($_GET['q']) && $result) {
                $searchQuery = urlencode($_GET['q']);
                header("Location: manage_cleaning_services.php?tab=search&q={$searchQuery}&message=updated");
                exit;
            } else {
                $activeTab = 'view';
            }

            // Refresh service list after successful update
            if ($result) {
                $servicesList = $serviceController->execute($cleanerId);
            }
        }
    }

    // Delete service
    else if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $serviceId = intval($_POST['service_id']);

        // Verify service ID belongs to this cleaner
        $serviceExists = false;
        foreach ($servicesList as $service) {
            if ($service['id'] == $serviceId) {
                $serviceExists = true;
                break;
            }
        }

        if (!$serviceExists) {
            $message = 'Invalid service ID or you do not have permission to delete this service';
            $messageType = 'error';
        } else {
            $result = (new DeleteCleaningServiceController())->execute($serviceId, $cleanerId);
            $message = $result ? 'Service deleted successfully' : 'Failed to delete service';
            $messageType = $result ? 'success' : 'error';

            // Refresh service list after successful deletion
            if ($result) {
                $servicesList = $serviceController->execute($cleanerId);
            }
        }

        $activeTab = 'view';
    }

    // Get service for editing
    else if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $serviceId = intval($_POST['service_id']);

        // Find the service in the list
        foreach ($servicesList as $service) {
            if ($service['id'] == $serviceId) {
                $serviceToEdit = $service;
                break;
            }
        }

        $activeTab = 'update';
        
        // Preserve search parameters if they exist
        if (isset($_POST['tab']) && $_POST['tab'] === 'search' && isset($_POST['q'])) {
            // We'll redirect after processing to maintain proper URL parameters
            $searchQuery = urlencode($_POST['q']);
            header("Location: manage_cleaning_services.php?tab=update&id={$serviceId}&from=search&q={$searchQuery}");
            exit;
        }
    }
}

// Handle search
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchResults = (new SearchCleaningServicesController())->execute($cleanerId, trim($_GET['q']));
    $activeTab = 'search';
    
    // Handle message parameter for redirects
    if (isset($_GET['message']) && $_GET['message'] === 'updated') {
        $message = 'Service updated successfully';
        $messageType = 'success';
    }
}

// Get service for editing from URL parameter
if ($activeTab === 'update' && isset($_GET['id']) && !$serviceToEdit) {
    $serviceId = intval($_GET['id']);

    // Find the service in the list
    foreach ($servicesList as $service) {
        if ($service['id'] == $serviceId) {
            $serviceToEdit = $service;
            break;
        }
    }
    
    // Check if this edit is from search results
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
        if (strpos($referer, 'tab=search') !== false && preg_match('/q=([^&]+)/', $referer, $matches)) {
            // Save the search query for the cancel button
            $searchQuery = $matches[1];
        }
    }
}
?>

<h2>Service Management</h2>

<!-- Tab Navigation -->
<div class="tab-navigation">
    <a href="?tab=view" class="tab-link <?= $activeTab === 'view' ? 'active' : '' ?>">View All</a>
    <a href="?tab=search" class="tab-link <?= $activeTab === 'search' ? 'active' : '' ?>">Search</a>
    <a href="?tab=create" class="tab-link <?= $activeTab === 'create' ? 'active' : '' ?>">Create New</a>
    <a href="?tab=update" class="tab-link <?= $activeTab === 'update' ? 'active' : '' ?>">Update</a>
</div>

<!-- Message Display -->
<?php if (!empty($message)): ?>
    <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- View All Services Tab -->
<?php if ($activeTab === 'view'): ?>
    <div class="card">
        <div class="card-title">All Your Services</div>

        <?php if (empty($servicesList)): ?>
            <div class="empty-state">
                <div class="empty-icon">🧹</div>
                <h3>No Services Found</h3>
                <p>You haven't created any cleaning services yet.</p>
                <a href="?tab=create" class="btn">Create New Service</a>
            </div>
        <?php else: ?>
            <div class="services-grid">
                <?php foreach ($servicesList as $service): ?>
                    <div class="service-card">
                        <div class="service-header">
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <span class="service-type"><?= htmlspecialchars($service['type']) ?></span>
                        </div>
                        <div class="service-price">$<?= htmlspecialchars($service['price']) ?></div>
                        <div class="service-id">ID: <?= htmlspecialchars($service['id']) ?></div>
                        <?php if (!empty($service['description'])): ?>
                            <div class="service-description"><?= htmlspecialchars($service['description']) ?></div>
                        <?php endif; ?>
                        <div class="service-actions">
                            <a href="?tab=update&id=<?= $service['id'] ?>" class="btn btn-small">Edit</a>
                            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.');">
                                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Search Tab -->
<?php if ($activeTab === 'search'): ?>
    <div class="card">
        <div class="card-title">Search Services</div>

        <form method="get" action="manage_cleaning_services.php" class="search-form">
            <input type="hidden" name="tab" value="search">
            <div class="form-group">
                <label for="q">Search by name, type, or description:</label>
                <div class="input-group">
                    <input type="text" id="q" name="q" placeholder="Enter name, type, or description..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
                    <button type="submit" class="btn">Search</button>
                </div>
            </div>
        </form>

        <?php if (isset($_GET['q'])): ?>
            <?php if (empty($searchResults)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3>No Results Found</h3>
                    <p>No services match your search criteria.</p>
                </div>
            <?php else: ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <div class="services-grid">
                        <?php foreach ($searchResults as $service): ?>
                            <div class="service-card">
                                <div class="service-header">
                                    <h3><?= htmlspecialchars($service['name']) ?></h3>
                                    <span class="service-type"><?= htmlspecialchars($service['type']) ?></span>
                                </div>
                                <div class="service-price">$<?= htmlspecialchars($service['price']) ?></div>
                                <div class="service-id">ID: <?= htmlspecialchars($service['id']) ?></div>
                                <?php if (!empty($service['description'])): ?>
                                    <div class="service-description"><?= htmlspecialchars($service['description']) ?></div>
                                <?php endif; ?>
                                <div class="service-actions">
                                    <a href="?tab=update&id=<?= $service['id'] ?>" class="btn btn-small">Edit</a>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.');">
                                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="tab" value="search">
                                        <input type="hidden" name="q" value="<?= htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '') ?>">
                                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Create Service Tab -->
<?php if ($activeTab === 'create'): ?>
    <div class="card">
        <div class="card-title">Create New Service</div>
        <form method="post" class="form">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="name">Service Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="type">Service Type</label>
                <select id="type" name="type" required>
                    <option value="">-- Select a Service Type --</option>
                    <?php foreach ($categoriesList as $category): ?>
                    <option value="<?= htmlspecialchars($category['name']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" min="0" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Create Service</button>
                <a href="?tab=view" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Update Service Tab -->
<?php if ($activeTab === 'update'): ?>
    <div class="card">
        <div class="card-title">Update Service</div>
        
        <?php if (!$serviceToEdit): ?>
            <div class="notice">
                <p>Select a service to edit:</p>
                <form method="post" class="form">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="tab" value="update">
                    
                    <div class="form-group">
                        <label for="service_select">Select Service</label>
                        <select id="service_select" name="service_id" required>
                            <option value="">-- Select a Service --</option>
                            <?php foreach ($servicesList as $service): ?>
                                <option value="<?= $service['id'] ?>">
                                    <?= htmlspecialchars($service['name']) ?> - $<?= htmlspecialchars($service['price']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">Select Service</button>
                        <a href="?tab=view" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <form method="post" class="form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="service_id" value="<?= $serviceToEdit['id'] ?>">
                
                <div class="form-group">
                    <label for="name">Service Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($serviceToEdit['name']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="type">Service Type</label>
                    <select id="type" name="type">
                        <option value="">-- Select a Service Type --</option>
                        <?php foreach ($categoriesList as $category): ?>
                        <option value="<?= htmlspecialchars($category['name']) ?>" <?= $serviceToEdit['type'] === $category['name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" value="<?= htmlspecialchars($serviceToEdit['price']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($serviceToEdit['description']) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Update Service</button>
                    <?php if (isset($searchQuery)): ?>
                        <a href="?tab=search&q=<?= urlencode($searchQuery) ?>" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <a href="?tab=view" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
/* Tab Navigation */
.tab-navigation {
    display: flex;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
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

/* Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.service-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #eee;
    padding: 0;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.service-header {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.service-header h3 {
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

.service-price {
    font-size: 24px;
    font-weight: bold;
    color: #34a853;
    padding: 15px 15px 5px 15px;
}

.service-id {
    font-size: 12px;
    color: #777;
    padding: 0 15px 10px 15px;
}

.service-description {
    padding: 0 15px 15px 15px;
    color: #555;
    font-size: 14px;
    border-bottom: 1px solid #eee;
}

.service-actions {
    padding: 15px;
    display: flex;
    justify-content: space-between;
}

/* Forms */
.service-form {
    max-width: 600px;
    margin: 0 auto;
}

.search-form {
    margin-bottom: 20px;
}

.input-group {
    display: flex;
}

.input-group input {
    flex: 1;
    border-radius: 4px 0 0 4px;
}

.input-group button {
    border-radius: 0 4px 4px 0;
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

.button-group {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

.required {
    color: #ea4335;
}

/* Alerts */
.alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
    border-left: 4px solid;
}

.alert-success {
    background-color: rgba(52, 168, 83, 0.1);
    color: #34a853;
    border-color: #34a853;
}

.alert-error {
    background-color: rgba(234, 67, 53, 0.1);
    color: #ea4335;
    border-color: #ea4335;
}

.alert-warning {
    background-color: rgba(251, 188, 5, 0.1);
    color: #fbbc05;
    border-color: #fbbc05;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
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

/* Search results */
.search-results {
    margin-top: 20px;
}

.search-results h3 {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

/* Service selector */
.service-selector {
    max-width: 600px;
    margin: 0 auto;
}

/* Form Styles */
.service-form select,
.service-selector select {
    display: block;
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    color: #333;
    background-color: white;
    transition: border-color 0.2s;
}

.service-form select:focus,
.service-selector select:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
}

.service-form select:hover,
.service-selector select:hover {
    border-color: #aaa;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
