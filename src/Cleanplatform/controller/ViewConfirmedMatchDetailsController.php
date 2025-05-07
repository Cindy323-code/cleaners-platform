<?php
namespace Controller;

use Entity\MatchHistory;

class ViewConfirmedMatchDetailsController {
    private MatchHistory $entity;

    public function __construct() {
        $this->entity = MatchHistory::getInstance();
    }

    public function execute(int $matchId) : ?array {
        return $this->entity->executeReadMatchDetails($matchId);
    }
}
