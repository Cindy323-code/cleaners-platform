<?php
// boundary/history/view_service_profile_views.php
namespace Boundary;

use Controller\ViewServiceProfileViewsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewServiceProfileViewsController.php';

$count=(new ViewServiceProfileViewsController())->execute($_SESSION['user']['id']);
?>
<html><body>
<h2>Profile View Count</h2><p><?=htmlspecialchars($count)?></p>
</body></html>
