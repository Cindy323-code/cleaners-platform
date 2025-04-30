<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class SearchServiceCategoryController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(string $keyword) : array {
        return $this->entity->searchCategory($keyword);
            }
        }
