<?php
// boundary/category/manage_service_types.php
namespace Boundary;

use Controller\ViewServiceCategoriesController;
use Controller\CreateServiceTypeController;
use Controller\UpdateServiceTypeController;
use Controller\DeleteServiceTypeController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/ViewServiceCategoriesController.php';

// Check if user is logged in as manager
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Get database connection
$db = \Config\Database::getConnection();

// Initialize variables
$message = '';
$messageType = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
$searchResults = [];
$typeToEdit = null;
$types = [];

// Get filter parameters
$minCount = isset($_GET['min_count']) ? intval($_GET['min_count']) : 0;
$maxCount = isset($_GET['max_count']) ? intval($_GET['max_count']) : 0;
$sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : 'type';
$sortDir = isset($_GET['dir']) && strtolower($_GET['dir']) === 'desc' ? 'DESC' : 'ASC';

// Handler for adding a new service type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = trim($_POST['name']);
    
    if (empty($name)) {
        $message = 'Type name is required';
        $messageType = 'error';
        $activeTab = 'create';
    } else {
        // Check if type already exists
        $checkSql = "SELECT COUNT(*) as count FROM cleaner_services WHERE type = ?";
        $stmt = mysqli_prepare($db, $checkSql);
        mysqli_stmt_bind_param($stmt, 's', $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_assoc($result)['count'];
        
        if ($count > 0) {
            $message = 'This service type already exists';
            $messageType = 'error';
            $activeTab = 'create';
        } else {
            // Insert a placeholder service with this type to make it available in the system
            $insertSql = "INSERT INTO cleaner_services (user_id, name, type, price, description, created_at) 
                          VALUES (1, 'Type Template', ?, 0.00, 'System template for type', NOW())";
            $stmt = mysqli_prepare($db, $insertSql);
            mysqli_stmt_bind_param($stmt, 's', $name);
            $success = mysqli_stmt_execute($stmt);
            
            // Delete the placeholder immediately - we just needed to register the type
            if ($success) {
                $lastId = mysqli_insert_id($db);
                $deleteSql = "DELETE FROM cleaner_services WHERE id = ?";
                $stmt = mysqli_prepare($db, $deleteSql);
                mysqli_stmt_bind_param($stmt, 'i', $lastId);
                mysqli_stmt_execute($stmt);
                
                // Add type directly to our list of available types
                $message = 'Service type created successfully';
                $messageType = 'success';
                $activeTab = 'view';
            } else {
                $message = 'Failed to create service type';
                $messageType = 'error';
                $activeTab = 'create';
            }
        }
    }
}

// Handler for updating a service type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $oldType = trim($_POST['old_type']);
    $newType = trim($_POST['new_type']);
    
    if (empty($newType)) {
        $message = 'Type name is required';
        $messageType = 'error';
        $activeTab = 'update';
    } else if ($oldType === $newType) {
        $message = 'No changes were made';
        $messageType = 'warning';
        $activeTab = 'update';
    } else {
        // Check if the new type already exists
        $checkSql = "SELECT COUNT(*) as count FROM cleaner_services WHERE type = ? AND type != ?";
        $stmt = mysqli_prepare($db, $checkSql);
        mysqli_stmt_bind_param($stmt, 'ss', $newType, $oldType);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_assoc($result)['count'];
        
        if ($count > 0) {
            $message = 'This service type already exists';
            $messageType = 'error';
            $activeTab = 'update';
        } else {
            // Update all services with this type
            $updateSql = "UPDATE cleaner_services SET type = ? WHERE type = ?";
            $stmt = mysqli_prepare($db, $updateSql);
            mysqli_stmt_bind_param($stmt, 'ss', $newType, $oldType);
            $success = mysqli_stmt_execute($stmt);
            
            if ($success) {
                $message = 'Service type updated successfully';
                $messageType = 'success';
                $activeTab = 'view';
            } else {
                $message = 'Failed to update service type';
                $messageType = 'error';
                $activeTab = 'update';
            }
        }
    }
}

// Handler for deleting a service type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $type = trim($_POST['type']);
    
    // Check if there are any services using this type
    $checkSql = "SELECT COUNT(*) as count FROM cleaner_services WHERE type = ?";
    $stmt = mysqli_prepare($db, $checkSql);
    mysqli_stmt_bind_param($stmt, 's', $type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count > 0 && !isset($_POST['delete_services'])) {
        // Services exist but user hasn't confirmed deletion of services
        $message = 'This type is used by ' . $count . ' service(s). Do you want to delete these services as well?';
        $messageType = 'warning';
        $typeToDelete = $type;
        $countToDelete = $count;
        $activeTab = 'delete_confirm';
    } else {
        // No services use this type OR user has confirmed deletion
        if ($count > 0) {
            // Delete all services with this type first
            $deleteServicesSql = "DELETE FROM cleaner_services WHERE type = ?";
            $stmt = mysqli_prepare($db, $deleteServicesSql);
            mysqli_stmt_bind_param($stmt, 's', $type);
            $success = mysqli_stmt_execute($stmt);
            
            if ($success) {
                $message = 'Service type and ' . $count . ' associated service(s) deleted successfully';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete services';
                $messageType = 'error';
            }
        } else {
            $message = 'Service type deleted successfully';
            $messageType = 'success';
        }
        $activeTab = 'view';
    }
}

// Handler for searching types
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search = '%' . trim($_GET['q']) . '%';
    
    // Build search query with filters
    $searchSql = "SELECT type, COUNT(*) as count FROM cleaner_services WHERE type LIKE ? GROUP BY type";
    
    // Apply filters if they exist
    $whereClause = [];
    $params = [$search];
    $paramTypes = 's';
    
    if ($minCount > 0) {
        $whereClause[] = "COUNT(*) >= ?";
        $params[] = $minCount;
        $paramTypes .= 'i';
    }
    
    if ($maxCount > 0) {
        $whereClause[] = "COUNT(*) <= ?";
        $params[] = $maxCount;
        $paramTypes .= 'i';
    }
    
    if (!empty($whereClause)) {
        $searchSql .= " HAVING " . implode(' AND ', $whereClause);
    }
    
    // Add ordering
    if ($sortBy === 'count') {
        $searchSql .= " ORDER BY COUNT(*) $sortDir, type ASC";
    } else {
        $searchSql .= " ORDER BY type $sortDir";
    }
    
    $stmt = mysqli_prepare($db, $searchSql);
    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $searchResults = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $searchResults[] = $row;
    }
    
    $activeTab = 'search';
}

// Handler for selecting a type to edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $typeToEdit = trim($_POST['type']);
    $activeTab = 'update';
}

// Get all service types for the view tab
$types = [];
if ($activeTab === 'view') {
    $typesSql = "SELECT DISTINCT type, COUNT(*) as count FROM cleaner_services GROUP BY type";
    
    // Apply filters if they exist
    $whereClause = [];
    $params = [];
    $paramTypes = '';
    
    if ($minCount > 0) {
        $whereClause[] = "COUNT(*) >= ?";
        $params[] = $minCount;
        $paramTypes .= 'i';
    }
    
    if ($maxCount > 0) {
        $whereClause[] = "COUNT(*) <= ?";
        $params[] = $maxCount;
        $paramTypes .= 'i';
    }
    
    if (!empty($whereClause)) {
        $typesSql .= " HAVING " . implode(' AND ', $whereClause);
    }
    
    // Add ordering
    $validSortColumns = ['type', 'count'];
    if (!in_array($sortBy, $validSortColumns)) {
        $sortBy = 'type';
    }
    
    if ($sortBy === 'count') {
        $typesSql .= " ORDER BY COUNT(*) $sortDir, type ASC";
    } else {
        $typesSql .= " ORDER BY type $sortDir";
    }
    
    if (!empty($params)) {
        $stmt = mysqli_prepare($db, $typesSql);
        mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($db, $typesSql);
    }
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $types[] = $row;
        }
    }
}
?>

<h2>Service Type Management</h2>

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

<!-- Delete Confirmation Tab -->
<?php if ($activeTab === 'delete_confirm'): ?>
    <div class="card">
        <div class="card-title">Confirm Deletion</div>
        <div class="alert alert-warning">
            <strong>Warning:</strong> You are about to delete the service type "<?= htmlspecialchars($typeToDelete) ?>" along with <?= $countToDelete ?> service(s) that use this type.
        </div>
        
        <div class="confirmation-actions">
            <form method="post">
                <input type="hidden" name="type" value="<?= htmlspecialchars($typeToDelete) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="delete_services" value="1">
                <button type="submit" class="btn btn-danger">Yes, Delete Type and Services</button>
                <a href="?tab=view" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- View All Types Tab -->
<?php if ($activeTab === 'view'): ?>
    <div class="card">
        <div class="card-title">All Service Types</div>
        
        <!-- Filtering Form -->
        <form method="get" class="filter-form">
            <input type="hidden" name="tab" value="view">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="min_count">Min Services:</label>
                    <input type="number" id="min_count" name="min_count" min="0" value="<?= $minCount > 0 ? htmlspecialchars($minCount) : '' ?>" placeholder="Min">
                </div>
                <div class="filter-group">
                    <label for="max_count">Max Services:</label>
                    <input type="number" id="max_count" name="max_count" min="0" value="<?= $maxCount > 0 ? htmlspecialchars($maxCount) : '' ?>" placeholder="Max">
                </div>
                <div class="filter-group">
                    <label for="sort">Sort By:</label>
                    <select id="sort" name="sort">
                        <option value="type" <?= $sortBy === 'type' ? 'selected' : '' ?>>Type Name</option>
                        <option value="count" <?= $sortBy === 'count' ? 'selected' : '' ?>>Service Count</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="dir">Direction:</label>
                    <select id="dir" name="dir">
                        <option value="asc" <?= $sortDir === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= $sortDir === 'DESC' ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-small">Apply Filters</button>
                    <a href="?tab=view" class="btn btn-small btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <?php if (empty($types)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>No Types Found</h3>
                <p>There are no service types in the system yet.</p>
                <a href="?tab=create" class="btn">Create New Type</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Type Name</th>
                            <th>Services Using This Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($types as $type): ?>
                            <tr>
                                <td><?= htmlspecialchars($type['type']) ?></td>
                                <td><?= htmlspecialchars($type['count']) ?></td>
                                <td class="actions">
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="type" value="<?= htmlspecialchars($type['type']) ?>">
                                        <input type="hidden" name="action" value="edit">
                                        <button type="submit" class="btn btn-small">Edit</button>
                                    </form>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this type?');">
                                        <input type="hidden" name="type" value="<?= htmlspecialchars($type['type']) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Search Tab -->
<?php if ($activeTab === 'search'): ?>
    <div class="card">
        <div class="card-title">Search Types</div>

        <form method="get" action="manage_service_types.php" class="search-form">
            <input type="hidden" name="tab" value="search">
            <div class="filter-row">
                <div class="filter-group search-input-group">
                    <label for="q">Search by name:</label>
                    <input type="text" id="q" name="q" placeholder="Enter keyword" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
                </div>
                <div class="filter-group">
                    <label for="min_count">Min Services:</label>
                    <input type="number" id="min_count" name="min_count" min="0" value="<?= $minCount > 0 ? htmlspecialchars($minCount) : '' ?>" placeholder="Min">
                </div>
                <div class="filter-group">
                    <label for="max_count">Max Services:</label>
                    <input type="number" id="max_count" name="max_count" min="0" value="<?= $maxCount > 0 ? htmlspecialchars($maxCount) : '' ?>" placeholder="Max">
                </div>
                <div class="filter-group">
                    <label for="sort">Sort By:</label>
                    <select id="sort" name="sort">
                        <option value="type" <?= $sortBy === 'type' ? 'selected' : '' ?>>Type Name</option>
                        <option value="count" <?= $sortBy === 'count' ? 'selected' : '' ?>>Service Count</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="dir">Direction:</label>
                    <select id="dir" name="dir">
                        <option value="asc" <?= $sortDir === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= $sortDir === 'DESC' ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn">Search</button>
                    <a href="?tab=search" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <?php if (isset($_GET['q'])): ?>
            <?php if (empty($searchResults)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3>No Results Found</h3>
                    <p>No types match your search criteria.</p>
                </div>
            <?php else: ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Type Name</th>
                                    <th>Services Using This Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $result): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($result['type']) ?></td>
                                        <td><?= htmlspecialchars($result['count']) ?></td>
                                        <td class="actions">
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="type" value="<?= htmlspecialchars($result['type']) ?>">
                                                <input type="hidden" name="action" value="edit">
                                                <button type="submit" class="btn btn-small">Edit</button>
                                            </form>
                                            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this type?');">
                                                <input type="hidden" name="type" value="<?= htmlspecialchars($result['type']) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Create Type Tab -->
<?php if ($activeTab === 'create'): ?>
    <div class="card">
        <div class="card-title">Create New Service Type</div>
        <form method="post" class="form">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="name">Type Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Create Type</button>
                <a href="?tab=view" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Update Type Tab -->
<?php if ($activeTab === 'update'): ?>
    <div class="card">
        <div class="card-title">Update Service Type</div>
        
        <?php if (!$typeToEdit): ?>
            <div class="notice">
                <p>Select a type to edit:</p>
                <form method="post" class="form">
                    <input type="hidden" name="action" value="edit">
                    
                    <div class="form-group">
                        <label for="type_select">Select Type</label>
                        <select id="type_select" name="type" required>
                            <option value="">-- Select a Type --</option>
                            <?php 
                            // Get all types for the dropdown
                            $typesSql = "SELECT DISTINCT type FROM cleaner_services ORDER BY type";
                            $result = mysqli_query($db, $typesSql);
                            
                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . htmlspecialchars($row['type']) . '">' . 
                                         htmlspecialchars($row['type']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">Select Type</button>
                        <a href="?tab=view" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <form method="post" class="form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="old_type" value="<?= htmlspecialchars($typeToEdit) ?>">
                
                <div class="form-group">
                    <label for="new_type">Type Name</label>
                    <input type="text" id="new_type" name="new_type" value="<?= htmlspecialchars($typeToEdit) ?>" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Update Type</button>
                    <a href="?tab=view" class="btn btn-secondary">Cancel</a>
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

/* Form styles */
.form {
    max-width: 600px;
    margin: 0 auto;
    padding: 15px;
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
    display: flex;
    gap: 10px;
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

/* Table styles */
.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.actions {
    white-space: nowrap;
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

/* Search form */
.search-form {
    margin-bottom: 20px;
    padding: 15px;
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

/* Add these styles at the end of the existing CSS */
.filter-form {
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #eee;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 120px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 12px;
    font-weight: bold;
    color: #555;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 5px;
    align-items: flex-end;
}

.confirmation-actions {
    padding: 20px;
    text-align: center;
}

.confirmation-actions form {
    display: inline-flex;
    gap: 10px;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

.search-input-group {
    flex: 2;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 