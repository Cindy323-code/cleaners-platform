<?php
namespace Controller;

use Entity\CleanerUser;

class DeleteCleaningServiceController {
    private CleanerUser $entity;

    public function __construct() {
        $this->entity = new CleanerUser();
    }

    public function execute(int $serviceId, int $cleanerId) : bool {
        // 确保只有服务所有者才能删除
        return $this->entity->executeDelete($serviceId, $cleanerId);
    }
}
