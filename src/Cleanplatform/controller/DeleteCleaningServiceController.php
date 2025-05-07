<?php
namespace Controller;

use Entity\CleanerUser;
use Entity\User;

class DeleteCleaningServiceController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = User::getInstance(['role' => 'cleaner']);
    }

    public function execute(int $serviceId, int $cleanerId) : bool {
        // 确保只有服务所有者才能删除
        return $this->entity->executeDelete($serviceId, $cleanerId);
    }
}
