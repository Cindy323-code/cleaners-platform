<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class CreateServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(string $name, string $description) : bool {
        return $this->entity->executeCreateCategory($name, $description);
    }
}
