<?php
namespace Controller;

use Entity\CleanerUser;

class CreateCleaningServiceController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute(array $data) : bool {
        return $this->entity->executeCreate($data);
    }
}
