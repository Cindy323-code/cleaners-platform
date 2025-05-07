<?php
namespace Controller;

use Entity\PlatformManager;

class UpdateServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(int $id, array $fields) : bool {
        return $this->entity->executeUpdateCategory($id, $fields);
    }
}
