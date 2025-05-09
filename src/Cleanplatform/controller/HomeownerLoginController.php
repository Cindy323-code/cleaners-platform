<?php
namespace Controller;

use Entity\User;

class HomeownerLoginController {
    
    public function execute(string $username, string $password): ?array {
        // Get homeowner entity and pass login credentials to it with required role
        $user = User::getInstance(['role' => 'homeowner']);
        return $user->executeLogin($username, $password, 'homeowner');
    }
}
