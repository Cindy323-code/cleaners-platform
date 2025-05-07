<?php
namespace Controller;

use Entity\HomeOwnerUser;

class SearchAvailableCleanersController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(array $criteria) : array {
        return $this->entity->executeSearchAvailableCleaners($criteria);
    }
}
