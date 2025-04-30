<?php
// boundary/shortlist/view_shortlist.php
namespace Boundary;

use Controller\ViewShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/ViewShortlistController.php';

$list=(new ViewShortlistController())->execute($_SESSION['user']['id']);
?>
<html><body>
<h2>Your Shortlist</h2>
<ul><?php foreach($list as$l):?><li><?=htmlspecialchars($l['name'])?></li><?php endforeach;?></ul>
</body></html>
