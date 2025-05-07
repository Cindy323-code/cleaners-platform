<?php
namespace Controller;

use Entity\CleanerUser;
use Entity\User;

class ViewCleaningServicesController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'cleaner']);
    }

    public function execute(int $cleanerId) : array {
        return $this->entity->execute($cleanerId);
    }
}
