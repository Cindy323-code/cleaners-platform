<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class RemoveFromShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(int $homeownerId, int $shortlistId) : bool {
        return $this->entity->executeRemoveFromShortlist($homeownerId, $shortlistId);
    }
}