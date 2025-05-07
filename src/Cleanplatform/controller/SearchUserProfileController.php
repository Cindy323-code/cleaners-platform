<?php
namespace Controller;

use Entity\UserProfile;

class SearchUserProfileController {
    private UserProfile $entity;

    public function __construct() {
        $this->entity = new UserProfile();
    }

    public function execute(array $criteria) : array {
        return $this->entity->executeSearch($criteria);
    }
}
