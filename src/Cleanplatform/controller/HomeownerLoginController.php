<?php
namespace Controller;

use Entity\HomeOwnerUser;
use Entity\User;

class HomeownerLoginController {
    private HomeOwnerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'homeowner']);
    }

    public function execute(string $username, string $password) : ?array {
        return $this->entity->executeLogin($username, $password);
    }
}
