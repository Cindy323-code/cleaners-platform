<?php
// boundary/history/search_service_usage_history.php
namespace Boundary\History;

use Controller\SearchServiceUsageHistoryController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/SearchServiceUsageHistoryController.php';

class SearchServiceUsageHistoryBoundary
{
    private SearchServiceUsageHistoryController $controller;

    public function __construct()
    {
        $this->controller = new SearchServiceUsageHistoryController();
    }

    public function render(array $context): void
    {
        // Set defaults for context variables
        $context['filters'] = $_GET;
        
        // Get service types for filter dropdown
        $context['service_types'] = ['Regular', 'Deep Clean', 'Office', 'Move-In/Out', 'Special'];
        $context['show_service_type'] = true;
        $context['show_date_filter'] = true;
        $context['show_status_filter'] = true;
        $context['show_person_filter'] = true;
        $context['person_label'] = 'Cleaner';
        $context['person_field'] = 'cleaner';
        $context['person_sort'] = 'cleaner';
        $context['show_date_sort'] = true;
        $context['reset_url'] = '?'; // Reset URL

        // Include the filter form
        include_once __DIR__ . '/../partials/filter_form.php';
        
        // Get and display the results
        $homeowner_id = $context['homeowner_id'];
        $results = $this->controller->execute($homeowner_id, $context['filters']);
        
        if (empty($results)) {
            echo '<div class="alert alert-info">No history records found matching your criteria.</div>';
        } else {
            $this->showResults($results);
        }
    }
    
    private function showResults(array $results): void
    {
        echo '<h3>Service Usage History</h3>';
        echo '<div class="results-container">';
        echo '<table class="results-table">';
        echo '<thead><tr>';
        echo '<th>ID</th>';
        echo '<th>Service Name</th>';
        echo '<th>Type</th>';
        echo '<th>Date</th>';
        echo '<th>Price</th>';
        echo '<th>Status</th>';
        echo '<th>Cleaner</th>';
        echo '<th>Actions</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        
        foreach ($results as $item) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['id']) . '</td>';
            echo '<td>' . htmlspecialchars($item['name']) . '</td>';
            echo '<td>' . htmlspecialchars($item['type']) . '</td>';
            echo '<td>' . htmlspecialchars($item['date']) . '</td>';
            echo '<td>$' . htmlspecialchars($item['price']) . '</td>';
            echo '<td>' . htmlspecialchars($item['status']) . '</td>';
            echo '<td>' . htmlspecialchars($item['cleaner_name']) . '</td>';
            echo '<td><a href="?action=viewUsageDetails&id=' . $item['id'] . '" class="btn-view">View Details</a></td>';
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