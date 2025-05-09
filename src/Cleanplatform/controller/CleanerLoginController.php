<?php
namespace Controller;

use Entity\User;

class CleanerLoginController {
    
    public function execute(string $username, string $password): ?array {
        // Get cleaner entity and pass login credentials to it with required role
        $user = User::getInstance(['role' => 'cleaner']);
        return $user->executeLogin($username, $password, 'cleaner');
    }
}
