<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class SearchServiceCategoryController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(string $keyword) : array {
        return $this->entity->executeSearchCategory($keyword);
    }
}
