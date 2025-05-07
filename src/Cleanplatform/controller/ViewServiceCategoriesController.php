<?php
namespace Controller;

use Entity\PlatformManager;

class ViewServiceCategoriesController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute() : array {
        return $this->entity->executeViewCategories();
    }
}
