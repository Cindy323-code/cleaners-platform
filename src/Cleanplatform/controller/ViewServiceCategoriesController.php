<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

require_once __DIR__ . '/../Entity/User.php';
require_once __DIR__ . '/../Entity/PlatformManager.php';

class ViewServiceCategoriesController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute() : array {
        return $this->entity->executeViewCategories();
    }
}
