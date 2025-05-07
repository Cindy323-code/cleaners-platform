<?php
namespace Controller;

use Entity\UserProfile;

class SearchUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = UserProfile::getInstance();
    }

    public function execute(array $criteria) : array {
        return $this->entity->executeSearch($criteria);
    }
}
