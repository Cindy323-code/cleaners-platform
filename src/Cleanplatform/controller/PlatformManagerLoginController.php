<?php
namespace Controller;

use Entity\PlatformManager;
use Entity\User;

class PlatformManagerLoginController {
    private PlatformManager $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'platform_manager']);
    }

    public function execute(string $username, string $password) : ?array {
        return $this->entity->executeLogin($username, $password);
    }
}
