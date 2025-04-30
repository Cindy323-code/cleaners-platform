<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class CreateServiceCategoryController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(string $name, string $description) : bool {
        return $this->entity->createCategory($name,$description);
            }
        }
