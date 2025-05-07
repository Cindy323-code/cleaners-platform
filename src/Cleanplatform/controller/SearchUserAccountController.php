<?php
namespace Controller;

use Entity\AdminUser;

class SearchUserAccountController {
    private AdminUser $entity;

    public function __construct() {
        $this->entity = new AdminUser();
    }

    public function execute(string $keyword = '', string $role = '', string $status = '') : array {
        return $this->entity->executeSearch($keyword, $role, $status);
    }
}
