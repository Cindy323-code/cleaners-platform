<?php
namespace Controller;

use Entity\CleanerUser;

class CleanerLoginController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute(string $username, string $password) : ?array {
        return $this->entity->executeLogin($username, $password);
    }
}
