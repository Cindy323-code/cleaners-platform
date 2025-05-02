<?php
// boundary/history/view_service_usage_details.php
namespace Boundary;

use Controller\ViewServiceUsageDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewServiceUsageDetailsController.php';

$matchId=intval($_GET['id']??0);
$details=$matchId?(new ViewServiceUsageDetailsController())->execute($matchId):null;
?>
<html><body>
<h2>All Usages</h2>
<?php if($details): ?><ul><?php foreach($details as$k=>$v):?><li><?=htmlspecialchars($k)?>: <?=htmlspecialchars($v)?></li><?php endforeach;?></ul><?php else:?><p>No details.</p><?php endif;?>
</body></html>