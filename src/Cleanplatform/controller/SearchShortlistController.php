<?php
namespace Controller;

use Entity\HomeOwnerUser;

class SearchShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(int $homeownerId, string $keyword) : array {
        return $this->entity->executeSearchShortlist($homeownerId, $keyword);
    }
}
