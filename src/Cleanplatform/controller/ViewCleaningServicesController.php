<?php
namespace Controller;

use Entity\CleanerUser;

class ViewCleaningServicesController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute(int $cleanerId) : array {
        return $this->entity->execute($cleanerId);
    }
}
