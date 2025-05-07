<?php
namespace Controller;

use Entity\PlatformManager;

class PlatformManagerLoginController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = new PlatformManager();
    }

    public function execute(string $username, string $password) : ?array {
        return $this->entity->executeLogin($username, $password);
    }
}
