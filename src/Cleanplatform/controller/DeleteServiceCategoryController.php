<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class DeleteServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(int $id) : bool {
        return $this->entity->executeDeleteCategory($id);
    }
}
