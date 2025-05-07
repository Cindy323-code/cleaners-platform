<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class ViewCleanerProfileController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(int $cleanerId) : ?array {
        return $this->entity->executeViewCleanerProfile($cleanerId);
    }
}
