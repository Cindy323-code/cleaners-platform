<?php
namespace Controller;

use Entity\HomeOwnerUser;

class RemoveFromShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(int $homeownerId, int $shortlistId) : bool {
        return $this->entity->executeRemoveFromShortlist($homeownerId, $shortlistId);
    }
}