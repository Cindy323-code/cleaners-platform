<?php
namespace Controller;

use Entity\MatchHistory;

class SearchServiceUsageHistoryController {
    private MatchHistory $entity;

    public function __construct() {
        $this->entity = MatchHistory::getInstance();
    }

    public function execute(int $homeownerId, array $filter) : array {
        return $this->entity->executeSearchUsageHistory($homeownerId, $filter);
    }
}
