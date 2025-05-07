<?php
namespace Controller;

use Entity\CleanerUser;
use Entity\User;

class UpdateCleaningServiceController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'cleaner']);
    }

    public function execute(int $serviceId, array $fields) : bool {
        return $this->entity->executeUpdate($serviceId, $fields);
    }
}
