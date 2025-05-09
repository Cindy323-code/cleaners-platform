<?php
namespace Controller;

use Entity\CleanerUser;
use Entity\User;

class CreateCleaningServiceController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'cleaner']);
    }

    public function execute(array $data) : bool {
        return $this->entity->executeCreateService($data);
    }
}
