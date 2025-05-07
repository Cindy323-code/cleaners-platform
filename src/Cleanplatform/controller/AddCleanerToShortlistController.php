<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class AddCleanerToShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(int $homeownerId, int $serviceId) : bool {
        return $this->entity->executeAddToShortlist($homeownerId, $serviceId);
    }
}
