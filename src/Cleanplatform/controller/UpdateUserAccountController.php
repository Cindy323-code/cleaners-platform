<?php
namespace Controller;

use Entity\AdminUser;
use Entity\User;
use Entity\UserProfile;
require_once __DIR__ . '/../Entity/UserProfile.php';

class UpdateUserAccountController {
    private AdminUser $adminEntity;

    public function __construct() {
        $this->adminEntity = User::getInstance(['role' => 'admin']);
    }

    public function execute(string $username, array $userFields, array $profileData = []) : bool {
        $userUpdateSuccess = true;

        if (!empty($userFields)) {
            $userUpdateSuccess = $this->adminEntity->executeUpdate($username, $userFields);
        }

        if ($userUpdateSuccess && !empty($profileData)) {
            $userData = $this->adminEntity->viewUser($username);
            if ($userData && isset($userData['id'])) {
                $userId = $userData['id'];
                $userProfileEntity = UserProfile::getInstance();
                return $userProfileEntity->execute($userId, $profileData);
            } else {
                return false;
            }
        }

        return $userUpdateSuccess;
    }
}
