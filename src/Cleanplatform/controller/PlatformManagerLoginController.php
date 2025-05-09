<?php
namespace Controller;

use Entity\User;

class PlatformManagerLoginController {
    
    public function execute(string $username, string $password): ?array {
        // Get manager entity and pass login credentials to it with required role
        $user = User::getInstance(['role' => 'manager']);
        return $user->executeLogin($username, $password, 'manager');
    }
}
