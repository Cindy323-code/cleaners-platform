<?php
// boundary/history/view_service_shortlist_count.php
namespace Boundary;

use Controller\ViewServiceShortlistCountController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewServiceShortlistCountController.php';

$count=(new ViewServiceShortlistCountController())->execute($_SESSION['user']['id']);
?>
<html><body>
<h2>Shortlist Count</h2><p><?=htmlspecialchars($count)?></p>
</body></html>