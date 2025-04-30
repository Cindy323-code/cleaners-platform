<?php
namespace Controller;

        use Config\Database;
use Entity\MatchHistory;

        class ViewServiceUsageDetailsController {
            private $db;
    private MatchHistory $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new MatchHistory($this->db);
            }

            public function execute(int $matchId) : ?array {
        return $this->entity->readHomeownerMatchDetails($matchId);
            }
        }
