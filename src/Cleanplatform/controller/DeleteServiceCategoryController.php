<?php
namespace Controller;

use Entity\PlatformManager;

class DeleteServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(int $id) : bool {
        return $this->entity->executeDeleteCategory($id);
    }
}
