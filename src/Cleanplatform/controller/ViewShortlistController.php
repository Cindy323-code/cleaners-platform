<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class ViewShortlistController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(int $homeownerId) : array {
        return $this->entity->executeViewShortlist($homeownerId);
    }
}
