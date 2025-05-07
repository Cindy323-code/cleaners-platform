<?php
namespace Controller;

use Entity\CleanerUser;

class UpdateCleaningServiceController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute(int $serviceId, array $fields) : bool {
        return $this->entity->executeUpdate($serviceId, $fields);
    }
}
