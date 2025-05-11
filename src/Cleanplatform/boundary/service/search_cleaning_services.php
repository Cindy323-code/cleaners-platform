<?php
// boundary/service/search_cleaning_services.php
namespace Boundary\Service;

use Controller\SearchCleaningServicesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/SearchCleaningServicesController.php';

class SearchCleaningServicesBoundary
{
    private SearchCleaningServicesController $controller;

    public function __construct()
    {
        $this->controller = new SearchCleaningServicesController();
    }

    public function render(array $context): void
    {
        // Set defaults for context variables
        $keyword = $context['keyword'] ?? '';
        $filters = [
            'price_min' => $_GET['price_min'] ?? '',
            'price_max' => $_GET['price_max'] ?? '',
            'type' => $_GET['type'] ?? '',
            'created_after' => $_GET['created_after'] ?? '',
            'sort_by' => $_GET['sort_by'] ?? '',
            'sort_dir' => $_GET['sort_dir'] ?? 'asc',
        ];
        
        // Get service types for filter dropdown
        $db = \Config\Database::getConnection();
        $cleanerId = $context['cleaner_id'];
        $typesSql = "SELECT DISTINCT type FROM cleaner_services WHERE user_id = ? ORDER BY type";
        $stmt = mysqli_prepare($db, $typesSql);
        mysqli_stmt_bind_param($stmt, 'i', $cleanerId);
        mysqli_stmt_execute($stmt);
        $typesResult = mysqli_stmt_get_result($stmt);
        $service_types = [];
        while ($type = mysqli_fetch_assoc($typesResult)) {
            $service_types[] = $type['type'];
        }
        mysqli_stmt_close($stmt);
        
        // Setup filter form context
        $filter_context = [
            'keyword' => $keyword,
            'filters' => $filters,
            'service_types' => $service_types,
            'show_service_type' => true,
            'show_date_filter' => false,
            'show_date_sort' => true,
            'reset_url' => '?',
        ];
        
        // Include the filter form
        include_once __DIR__ . '/../partials/filter_form.php';
        
        // Get and display the results
        $results = $this->controller->execute($cleanerId, $keyword, $filters);
        
        if (empty($results)) {
            echo '<div class="alert alert-info">No services found matching your criteria.</div>';
        } else {
            $this->showResults($results);
        }
    }
    
    private function showResults(array $results): void
    {
        echo '<h3>Matching Services</h3>';
        echo '<div class="results-container">';
        echo '<table class="results-table">';
        echo '<thead><tr>';
        echo '<th>ID</th>';
        echo '<th>Service Name</th>';
        echo '<th>Type</th>';
        echo '<th>Price</th>';
        echo '<th>Description</th>';
        echo '<th>Actions</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        
        foreach ($results as $item) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['id']) . '</td>';
            echo '<td>' . htmlspecialchars($item['name']) . '</td>';
            echo '<td>' . htmlspecialchars($item['type']) . '</td>';
            echo '<td>$' . htmlspecialchars($item['price']) . '</td>';
            echo '<td>' . htmlspecialchars($item['description']) . '</td>';
            echo '<td><a href="?action=edit&id=' . $item['id'] . '" class="btn-view">Edit</a></td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        echo '</div>';
    }
}

// Style for results table
echo '<style>
.results-container {
    margin-top: 20px;
}

.results-table {
    width: 100%;
    border-collapse: collapse;
}

.results-table th, .results-table td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}

.results-table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.results-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.results-table tr:hover {
    background-color: #f1f1f1;
}

.btn-view {
    display: inline-block;
    padding: 5px 10px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 3px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}
</style>';