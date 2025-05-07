<?php
namespace Controller;

use Entity\HomeOwnerUser;

class ViewShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(int $homeownerId) : array {
        return $this->entity->executeViewShortlist($homeownerId);
    }
}
