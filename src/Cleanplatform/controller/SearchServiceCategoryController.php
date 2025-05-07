<?php
namespace Controller;

use Entity\PlatformManager;

class SearchServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(string $keyword) : array {
        return $this->entity->executeSearchCategory($keyword);
    }
}
