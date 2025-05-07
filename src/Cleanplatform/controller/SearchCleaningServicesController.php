<?php
namespace Controller;

use Entity\CleanerUser;

class SearchCleaningServicesController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute(int $cleanerId, string $keyword) : array {
        return $this->entity->executeSearch($cleanerId, $keyword);
    }
}
