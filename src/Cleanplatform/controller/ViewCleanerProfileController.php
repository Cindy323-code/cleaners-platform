<?php
namespace Controller;

use Entity\HomeOwnerUser;

class ViewCleanerProfileController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(int $cleanerId) : ?array {
        return $this->entity->executeViewCleanerProfile($cleanerId);
    }
}
