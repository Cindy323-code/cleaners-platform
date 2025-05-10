<?php
// boundary/category/manage_service_categories.php
namespace Boundary;

use Controller\ViewServiceCategoriesController;
use Controller\CreateServiceCategoryController;
use Controller\UpdateServiceCategoryController;
use Controller\DeleteServiceCategoryController;
use Controller\SearchServiceCategoryController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/ViewServiceCategoriesController.php';
require_once __DIR__ . '/../../controller/CreateServiceCategoryController.php';
require_once __DIR__ . '/../../controller/UpdateServiceCategoryController.php';
require_once __DIR__ . '/../../controller/DeleteServiceCategoryController.php';
require_once __DIR__ . '/../../controller/SearchServiceCategoryController.php';

// Check if user is logged in as manager
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

// Initialize variables
$message = '';
$messageType = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
$searchResults = [];
$categoryToEdit = null;
$categories = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create category
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);

        if (empty($name)) {
            $message = 'Category name is required';
            $messageType = 'error';
            $activeTab = 'create';
        } else {
            $result = (new CreateServiceCategoryController())->execute($name, $desc);
            $message = $result ? 'Category created successfully' : 'Failed to create category';
            $messageType = $result ? 'success' : 'error';
            $activeTab = $result ? 'view' : 'create';
        }
    }

    // Update category
    else if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = intval($_POST['id']);
        $fields = [];

        if (!empty($_POST['name'])) {
            $fields['name'] = trim($_POST['name']);
        }

        if (isset($_POST['description'])) {
            $fields['description'] = trim($_POST['description']);
        }

        if (empty($fields)) {
            $message = 'No changes were made';
            $messageType = 'warning';
            $activeTab = 'update';
        } else {
            $result = (new UpdateServiceCategoryController())->execute($id, $fields);
            $message = $result ? 'Category updated successfully' : 'Failed to update category';
            $messageType = $result ? 'success' : 'error';
            $activeTab = $result ? 'view' : 'update';
        }
    }

    // Delete category
    else if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $result = (new DeleteServiceCategoryController())->execute($id);
        $message = $result ? 'Category deleted successfully' : 'Failed to delete category';
        $messageType = $result ? 'success' : 'error';
        $activeTab = 'view';
    }

    // Get category for editing
    else if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = intval($_POST['id']);
        // Find the category in the list
        $allCategories = (new ViewServiceCategoriesController())->execute();
        foreach ($allCategories as $category) {
            if ($category['id'] == $id) {
                $categoryToEdit = $category;
                break;
            }
        }
        $activeTab = 'update';
    }
}

// Handle search
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchResults = (new SearchServiceCategoryController())->execute(trim($_GET['q']));
    $activeTab = 'search';
}

// Handle edit from search results
if (isset($_POST['action']) && $_POST['action'] === 'edit' && $activeTab === 'search') {
    $id = intval($_POST['id']);
    // Find the category in the search results
    foreach ($searchResults as $result) {
        if ($result['id'] == $id) {
            $categoryToEdit = $result;
            break;
        }
    }
    $activeTab = 'update';
}

// Get all categories for the view tab
if ($activeTab === 'view') {
    $categories = (new ViewServiceCategoriesController())->execute();
}
?>

<h2>Service Category Management</h2>

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

<!-- View All Categories Tab -->
<?php if ($activeTab === 'view'): ?>
    <div class="card">
        <div class="card-title">All Service Categories</div>

        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>No Categories Found</h3>
                <p>There are no service categories in the system yet.</p>
                <a href="?tab=create" class="btn">Create New Category</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['id']) ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['description'] ?? '') ?></td>
                                <td><?= htmlspecialchars($category['created_at'] ?? '') ?></td>
                                <td class="actions">
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                        <input type="hidden" name="action" value="edit">
                                        <button type="submit" class="btn btn-small">Edit</button>
                                    </form>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
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
        <div class="card-title">Search Categories</div>

        <form method="get" action="manage_service_categories.php" class="search-form">
            <input type="hidden" name="tab" value="search">
            <div class="form-group">
                <label for="q">Search by name:</label>
                <div class="input-group">
                    <input type="text" id="q" name="q" placeholder="Enter keyword" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
                    <button type="submit" class="btn">Search</button>
                </div>
            </div>
        </form>

        <?php if (isset($_GET['q'])): ?>
            <?php if (empty($searchResults)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3>No Results Found</h3>
                    <p>No categories match your search criteria.</p>
                </div>
            <?php else: ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $result): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($result['id']) ?></td>
                                        <td><?= htmlspecialchars($result['name']) ?></td>
                                        <td><?= htmlspecialchars($result['description'] ?? '') ?></td>
                                        <td class="actions">
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $result['id'] ?>">
                                                <input type="hidden" name="action" value="edit">
                                                <button type="submit" class="btn btn-small">Edit</button>
                                            </form>
                                            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                <input type="hidden" name="id" value="<?= $result['id'] ?>">
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

<!-- Create Category Tab -->
<?php if ($activeTab === 'create'): ?>
    <div class="card">
        <div class="card-title">Create New Category</div>

        <form method="post" action="manage_service_categories.php" class="category-form">
            <input type="hidden" name="action" value="create">

            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter category name" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Enter category description" rows="4"></textarea>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Create Category</button>
                <a href="?tab=view" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Update Category Tab -->
<?php if ($activeTab === 'update'): ?>
    <div class="card">
        <div class="card-title">Update Category</div>

        <?php if ($categoryToEdit): ?>
            <form method="post" action="manage_service_categories.php" class="category-form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $categoryToEdit['id'] ?>">

                <div class="form-group">
                    <label for="name">Category Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter category name" value="<?= htmlspecialchars($categoryToEdit['name']) ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" placeholder="Enter category description" rows="4"><?= htmlspecialchars($categoryToEdit['description'] ?? '') ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Update Category</button>
                    <a href="?tab=view" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <form method="post" action="manage_service_categories.php" class="category-form">
                <input type="hidden" name="action" value="edit">

                <div class="form-group">
                    <label for="id">Category ID:</label>
                    <input type="number" id="id" name="id" placeholder="Enter category ID" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Find Category</button>
                    <a href="?tab=view" class="btn btn-secondary"">Cancel</a>
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

/* Forms */
.category-form {
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

/* Tables */
.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
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

.actions {
    white-space: nowrap;
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

/* Button Group Styling */
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
