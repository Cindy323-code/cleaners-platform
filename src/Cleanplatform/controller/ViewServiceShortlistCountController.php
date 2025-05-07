<?php
namespace Controller;

use Entity\MatchHistory;

class ViewServiceShortlistCountController {
    private MatchHistory $entity;

    public function __construct() {
        $this->entity = new MatchHistory();
    }

    public function execute(int $cleanerId) : int {
        return $this->entity->executeGetShortlistCount($cleanerId);
    }
}
