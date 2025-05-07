<?php
namespace Controller;

use Entity\CleanerUser;
use Entity\User;

class SearchCleaningServicesController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'cleaner']);
    }

    public function execute(int $cleanerId, string $keyword) : array {
        return $this->entity->executeSearch($cleanerId, $keyword);
    }
}
