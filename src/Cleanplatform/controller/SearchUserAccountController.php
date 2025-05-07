<?php
namespace Controller;

use Entity\AdminUser;
use Entity\User;

class SearchUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'admin']);
    }

    public function execute(string $keyword = '', string $role = '', string $status = '') : array {
        return $this->entity->executeSearch($keyword, $role, $status);
    }
}
