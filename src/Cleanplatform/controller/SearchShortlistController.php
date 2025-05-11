<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class SearchShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(int $userId, string $keyword, array $filters = []) : array {
        return $this->entity->executeSearchShortlist($userId, $keyword, $filters);
    }
}
