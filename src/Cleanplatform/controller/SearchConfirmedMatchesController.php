<?php
namespace Controller;

        use Config\Database;
use Entity\MatchHistory;

        class SearchConfirmedMatchesController {
            private $db;
    private MatchHistory $entity;

            public function __construct() {
                $this->db = Database::getConnection();
        $this->entity = new MatchHistory($this->db);
            }

            public function execute(int $cleanerId, array $filter) : array {
        return $this->entity->searchCleanerHistory($cleanerId,$filter);
            }
        }
