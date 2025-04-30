<?php
namespace Controller;

        use Config\Database;
use Entity\PlatformManager;

        class UpdateServiceCategoryController {
            private $db;
    private PlatformManager $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new PlatformManager($this->db);
            }

            public function execute(int $id, array $fields) : bool {
        return $this->entity->updateCategory($id,$fields);
            }
        }
