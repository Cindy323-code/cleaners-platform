<?php
namespace Controller;

use Entity\MatchHistory;

class ViewServiceUsageDetailsController {
    private MatchHistory $entity;

    public function __construct() {
        $this->entity = new MatchHistory();
    }

    public function execute(int $matchId) : ?array {
        return $this->entity->executeGetUsageDetails($matchId);
    }
}
