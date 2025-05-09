<?php
namespace Controller;

use Entity\User;

// controller/UserLoginController.php
class UserLoginController {
    
    public function execute(string $username, string $password): ?array {
        // Get admin entity and pass login credentials to it
        $user = User::getInstance(['role' => 'admin']);
        return $user->executeLogin($username, $password, 'admin');
    }
}

