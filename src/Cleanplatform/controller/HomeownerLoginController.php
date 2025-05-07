<?php
namespace Controller;

use Entity\HomeOwnerUser;

class HomeownerLoginController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = new HomeOwnerUser();
    }

    public function execute(string $username, string $password) : ?array {
        return $this->entity->executeLogin($username, $password);
    }
}
