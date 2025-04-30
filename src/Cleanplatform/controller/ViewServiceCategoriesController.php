<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class ViewServiceCategoriesController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute() : array {
        return $this->entity->viewCategories();
            }
        }
