<?php
namespace Controller;

use Entity\MatchHistory;

class SearchConfirmedMatchesController {
    private MatchHistory $entity;

    public function __construct() {
        $this->entity = MatchHistory::getInstance();
    }

    public function execute(int $cleanerId, array $filter) : array {
        return $this->entity->executeSearchConfirmedMatches($cleanerId, $filter);
    }
}
