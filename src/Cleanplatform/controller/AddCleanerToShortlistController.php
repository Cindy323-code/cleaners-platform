<?php
namespace Controller;

use Entity\HomeOwnerUser;

class AddCleanerToShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(int $homeownerId, int $serviceId) : bool {
        return $this->entity->executeAddToShortlist($homeownerId, $serviceId);
    }
}
