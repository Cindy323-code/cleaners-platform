<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class ViewServiceCategoriesController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute() : array {
        return $this->entity->executeViewCategories();
    }
}
