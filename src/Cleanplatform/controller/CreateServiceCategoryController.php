<?php
namespace Controller;

use Entity\PlatformManager;

class CreateServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(string $name, string $description) : bool {
        return $this->entity->executeCreateCategory($name, $description);
    }
}
