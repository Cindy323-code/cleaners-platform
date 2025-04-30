<?php
// boundary/history/view_confirmed_match_details.php
namespace Boundary;

use Controller\ViewConfirmedMatchDetailsController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/ViewConfirmedMatchDetailsController.php';

$matchId=intval($_GET['id'] ?? 0);
$details= $matchId ? (new ViewConfirmedMatchDetailsController())->execute($matchId) : null;
?>
<html><body>
<h2>Match Details</h2>
<?php if($details): ?><ul><?php foreach($details as$k=>$v):?><li><?=htmlspecialchars($k)?>: <?=htmlspecialchars($v)?></li><?php endforeach;?></ul><?php else:?><p>No details.</p><?php endif;?>
</body></html>