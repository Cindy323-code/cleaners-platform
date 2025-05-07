<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class SearchAvailableCleanersController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(array $criteria) : array {
        return $this->entity->executeSearchAvailableCleaners($criteria);
    }
}
