<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;
use Config\Database;

require_once __DIR__ . '/../Entity/User.php';
require_once __DIR__ . '/../config/Database.php';

class ViewServiceCategoriesController {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Get all unique service types from cleaner_services table
     * This replaces the old category lookup with type values
     */
    public function execute() : array {
        $sql = 'SELECT DISTINCT type as name FROM cleaner_services ORDER BY type';
        $result = mysqli_query($this->conn, $sql);
        
        $types = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Adding a structure similar to the old categories format for compatibility
                $types[] = [
                    'id' => null, // No longer has an ID
                    'name' => $row['name'],
                    'description' => null // No longer has a description
                ];
            }
        }
        
        // If no types are found, return some default types to prevent errors
        if (empty($types)) {
            $defaultTypes = ['Residential Cleaning', 'Commercial Cleaning', 'Deep Cleaning', 
                            'Post-Construction Cleaning', 'Window Cleaning', 'Carpet Cleaning',
                            'Move-In/Move-Out Cleaning', 'Appliance Cleaning', 'Bathroom Sanitization', 
                            'Kitchen Cleaning'];
            
            foreach ($defaultTypes as $type) {
                $types[] = [
                    'id' => null,
                    'name' => $type,
                    'description' => null
                ];
            }
        }
        
        return $types;
    }
}
