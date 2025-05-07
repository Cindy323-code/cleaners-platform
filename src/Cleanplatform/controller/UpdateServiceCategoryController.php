<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class UpdateServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(int $id, array $fields) : bool {
        return $this->entity->executeUpdateCategory($id, $fields);
    }
}
