<?php
namespace Controller;

use Config\Database;

class CleanerLogoutController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function execute() : void {
return session_destroy();
    }
}
